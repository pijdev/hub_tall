<?php

use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Marca')] class extends Component {
    use WithFileUploads;

    public string $name = '';

    public string $description = '';

    public string $tagline = '';

    public string $logo_text = '';

    public string $logo_gradient_from = '';

    public string $logo_gradient_to = '';

    public string $logo_gradient_from_hex = '';

    public string $logo_gradient_to_hex = '';

    public bool $ui_animations = true;

    public $temporaryLogo = null;

    public ?string $logo_preview_url = null;

    /** Imagem cortada em base64 (data URL) enviada pelo Cropper.js */
    public ?string $cropped_logo = null;

    public function mount(): void
    {
        $this->name = branding('name');
        $this->description = branding('description');
        $this->tagline = branding('tagline');
        $this->logo_text = branding('logo.text');
        $this->logo_gradient_from = branding('logo.gradient_from');
        $this->logo_gradient_to = branding('logo.gradient_to');
        $this->logo_gradient_from_hex = branding('logo.gradient_from_hex');
        $this->logo_gradient_to_hex = branding('logo.gradient_to_hex');
        $this->ui_animations = (bool) branding('ui.animations', true);
        $this->logo_preview_url = branding('logo.url');
    }

    public function updatedTemporaryLogo(): void
    {
        $this->validate([
            'temporaryLogo' => ['image', 'mimes:png,jpg,jpeg,gif,webp', 'max:5120'],
        ]);

        $this->logo_preview_url = $this->temporaryLogo->temporaryUrl();
        $this->cropped_logo = null;
    }

    public function removeLogo(): void
    {
        $this->temporaryLogo = null;
        $this->logo_preview_url = null;
        $this->cropped_logo = null;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'tagline' => ['nullable', 'string', 'max:200'],
            'logo_text' => ['required', 'string', 'max:5'],
            'logo_gradient_from' => ['required', 'string', 'max:50'],
            'logo_gradient_to' => ['required', 'string', 'max:50'],
            'logo_gradient_from_hex' => ['required', 'string', 'max:7'],
            'logo_gradient_to_hex' => ['required', 'string', 'max:7'],
            'ui_animations' => ['boolean'],
        ]);

        // Write text values to .env
        set_env('APP_NAME', $this->name);
        set_env('APP_TAGLINE', $this->tagline);
        set_env('APP_DESCRIPTION', $this->description);

        // Process logo
        $logoUrl = $this->logo_preview_url;

        if ($this->temporaryLogo) {
            $logoUrl = $this->storeLogo();
        }

        // Persist overrides in branding.json
        $data = [
            'logo' => [
                'text' => $this->logo_text,
                'gradient_from' => $this->logo_gradient_from,
                'gradient_to' => $this->logo_gradient_to,
                'gradient_from_hex' => $this->logo_gradient_from_hex,
                'gradient_to_hex' => $this->logo_gradient_to_hex,
                'url' => $logoUrl,
            ],
            'ui' => [
                'animations' => $this->ui_animations,
            ],
        ];

        Storage::put('branding.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Force config to reload for the remainder of the request
        config([
            'branding.name' => $this->name,
            'branding.tagline' => $this->tagline,
            'branding.description' => $this->description,
        ]);

        $this->logo_preview_url = $logoUrl;
        $this->temporaryLogo = null;
        $this->cropped_logo = null;

        Flux::toast(variant: 'success', text: __('Branding atualizado com sucesso.'));
    }

    public function resetToDefaults(): void
    {
        if (Storage::exists('branding.json')) {
            Storage::delete('branding.json');
        }

        Storage::deleteDirectory('branding');

        $this->mount();

        Flux::toast(variant: 'success', text: __('Branding restaurado aos valores padrão.'));
    }

    private function storeLogo(): string
    {
        $sourcePath = $this->temporaryLogo->getRealPath();

        // ── GIF: save as-is, no browser cropping ──
        $mime = $this->temporaryLogo->getMimeType();
        if ($mime === 'image/gif') {
            Storage::delete(['branding/logo.webp', 'branding/logo.png', 'branding/logo-original.webp', 'branding/logo-original.png', 'branding/logo-original.jpg', 'branding/logo-original.jpeg']);
            Storage::put('branding/logo.gif', file_get_contents($sourcePath));
            return route('branding.logo');
        }

        // ── Non-GIF: use cropped data from browser, or original ──
        Storage::delete('branding/logo.gif');

        if ($this->cropped_logo) {
            // Decode base64 data URL (e.g., "data:image/webp;base64,...")
            $decoded = $this->decodeBase64Image($this->cropped_logo);
            if ($decoded === null) {
                throw new \RuntimeException('Failed to decode cropped image data.');
            }
            [$binaryData, $format] = $decoded;
        } else {
            // Fallback: use original file (no crop applied)
            $info = getimagesize($sourcePath);
            $format = $info[2] === IMAGETYPE_PNG ? 'png' : 'webp';
            $binaryData = file_get_contents($sourcePath);
        }

        // Save WEBP and PNG
        $gd = imagecreatefromstring($binaryData);
        if ($gd === false) {
            throw new \RuntimeException('Failed to decode image data.');
        }

        if (imageistruecolor($gd) === false) {
            imagepalettetotruecolor($gd);
        }

        // Resize to 200×200 if needed
        $w = imagesx($gd);
        $h = imagesy($gd);
        $maxSize = 200;

        if ($w !== $maxSize || $h !== $maxSize) {
            $resized = imagecreatetruecolor($maxSize, $maxSize);
            imagecopyresampled($resized, $gd, 0, 0, 0, 0, $maxSize, $maxSize, $w, $h);
            imagedestroy($gd);
            $gd = $resized;
        }

        // Save via temp files
        $tempWebp = tempnam(sys_get_temp_dir(), 'branding_') . '.webp';
        $tempPng = tempnam(sys_get_temp_dir(), 'branding_') . '.png';
        imagewebp($gd, $tempWebp, 85);
        imagepng($gd, $tempPng, 6);

        if (file_exists($tempWebp) && filesize($tempWebp) > 100) {
            Storage::put('branding/logo.webp', file_get_contents($tempWebp));
        }
        if (file_exists($tempPng) && filesize($tempPng) > 100) {
            Storage::put('branding/logo.png', file_get_contents($tempPng));
        }

        @unlink($tempWebp);
        @unlink($tempPng);
        imagedestroy($gd);

        // Also save original
        $originalExt = pathinfo($sourcePath, PATHINFO_EXTENSION);
        Storage::put('branding/logo-original.' . $originalExt, file_get_contents($sourcePath));

        return route('branding.logo');
    }

    private function decodeBase64Image(string $dataUrl): ?array
    {
        // Expects "data:image/{format};base64,{data}"
        if (!preg_match('/^data:image\/(\w+);base64,(.+)$/', $dataUrl, $matches)) {
            return null;
        }
        $format = $matches[1];
        $binary = base64_decode($matches[2], true);
        if ($binary === false) {
            return null;
        }
        return [$binary, $format];
    }
}; ?>

{{-- Cropper.js — loaded via npm bundle --}}
<section class="w-full" x-data="{
    name: $wire.entangle('name'),
    tagline: $wire.entangle('tagline'),
    description: $wire.entangle('description'),
    logo_text: $wire.entangle('logo_text'),
    logo_gradient_from: $wire.entangle('logo_gradient_from'),
    logo_gradient_to: $wire.entangle('logo_gradient_to'),
    logo_gradient_from_hex: $wire.entangle('logo_gradient_from_hex'),
    logo_gradient_to_hex: $wire.entangle('logo_gradient_to_hex'),
    ui_animations: $wire.entangle('ui_animations'),
    logoPreviewUrl: $wire.entangle('logo_preview_url'),
    hasImage() { return this.logoPreviewUrl && this.logoPreviewUrl !== ''; },
    cropApplied: false,
    gradientStyle() {
        const from = this.logo_gradient_from_hex || '#ef4444';
        const to = this.logo_gradient_to_hex || '#f97316';
        return 'background: linear-gradient(to bottom right, ' + from + ', ' + to + ')';
    }
}" x-init="$watch('logoPreviewUrl', function(val) {
    this.cropApplied = false;
});">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Marca') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Marca')" :subheading="__('Personalize a identidade visual do sistema.')">
        <form wire:submit="save" class="space-y-10 pb-8">
            {{-- Informações gerais --}}
            <div>
                <flux:heading size="lg">{{ __('Informações gerais') }}</flux:heading>
                <flux:subheading>
                    {{ __('Nome, tagline e descrição do sistema — lidos do .env e salvos diretamente nele.') }}
                </flux:subheading>

                <div class="mt-4 space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Nome do sistema') }}</flux:label>
                        <flux:input x-model="name" placeholder="HUB" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Tagline') }}</flux:label>
                        <flux:input x-model="tagline" placeholder="Starter Kit Administrativo" />
                        <flux:error name="tagline" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Descrição') }}</flux:label>
                        <flux:textarea x-model="description" rows="3"
                            placeholder="Um ponto de partida seguro e moderno..." />
                        <flux:error name="description" />
                    </flux:field>
                </div>
            </div>

            {{-- Logo --}}
            <div>
                <flux:heading size="lg">{{ __('Logo do sistema') }}</flux:heading>
                <flux:subheading>
                    {{ __('Clique no avatar para fazer upload. Ou personalize a letra e gradiente do logo padrão.') }}
                </flux:subheading>

                <div class="mt-6">
                    {{-- Avatar upload --}}
                    <div class="flex flex-wrap items-end gap-6">
                        {{-- Preview clicável --}}
                        <div class="relative shrink-0">
                            <template x-if="hasImage()">
                                <div class="relative group cursor-pointer" x-on:click="$refs.fileInput.click()">
                                    <img :src="logoPreviewUrl"
                                        class="size-24 rounded-xl object-cover border-2 border-zinc-200 dark:border-zinc-700"
                                        alt="{{ __('Logo') }}" />
                                    <div
                                        class="absolute inset-0 flex items-center justify-center rounded-xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="size-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <button type="button" x-on:click.stop="$wire.removeLogo()"
                                        class="absolute -top-2 -right-2 flex size-5 items-center justify-center rounded-full bg-red-500 text-white text-xs shadow hover:bg-red-600 transition-colors"
                                        aria-label="{{ __('Remover logo') }}">
                                        &times;
                                    </button>
                                </div>
                            </template>

                            <template x-if="!hasImage()">
                                <div class="relative group cursor-pointer" x-on:click="$refs.fileInput.click()">
                                    <span
                                        class="flex size-24 items-center justify-center rounded-xl text-4xl font-bold text-white border-2 border-dashed border-zinc-300 dark:border-zinc-600"
                                        :style="gradientStyle()" x-text="logo_text || 'H'">
                                    </span>
                                    <div
                                        class="absolute inset-0 flex items-center justify-center rounded-xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="size-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                </div>
                            </template>

                            <flux:text class="mt-2 text-center text-xs">{{ __('Clique para alterar') }}</flux:text>

                            {{-- Hidden file input --}}
                            <input type="file" wire:model="temporaryLogo"
                                accept="image/png,image/jpeg,image/gif,image/webp" x-ref="fileInput" class="hidden" />
                            <flux:error name="temporaryLogo" />
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <flux:text class="text-sm">
                                {{ __('PNG, JPG, GIF ou WEBP — máx. 5 MB. A imagem será ajustada para 200×200.') }}
                            </flux:text>
                        </div>
                    </div>

                    {{-- Cropper (aparece após upload) — CropperJS v2 com custom elements --}}
                    <template x-if="hasImage()">
                        <div class="mt-4 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                            <cropper-canvas class="block bg-zinc-50 dark:bg-zinc-900 [--cropper-canvas-min-width:100%]"
                                background>
                                <cropper-image id="cropper-source" :src="logoPreviewUrl"
                                    alt="{{ __('Cortar imagem') }}" rotatable scalable translatable>
                                </cropper-image>
                                <cropper-shade hidden></cropper-shade>
                                <cropper-handle action="select" plain></cropper-handle>
                                <cropper-selection id="cropper-selection" initial-coverage="0.5" movable resizable
                                    aspect-ratio="1">
                                    <cropper-grid role="grid" bordered covered></cropper-grid>
                                    <cropper-crosshair centered></cropper-crosshair>
                                    <cropper-handle action="move"
                                        theme-color="rgba(255, 255, 255, 0.35)"></cropper-handle>
                                    <cropper-handle action="n-resize"></cropper-handle>
                                    <cropper-handle action="e-resize"></cropper-handle>
                                    <cropper-handle action="s-resize"></cropper-handle>
                                    <cropper-handle action="w-resize"></cropper-handle>
                                    <cropper-handle action="ne-resize"></cropper-handle>
                                    <cropper-handle action="nw-resize"></cropper-handle>
                                    <cropper-handle action="se-resize"></cropper-handle>
                                    <cropper-handle action="sw-resize"></cropper-handle>
                                </cropper-selection>
                            </cropper-canvas>

                            {{-- Controles de zoom e rotação --}}
                            <div
                                class="flex items-center justify-center gap-1 border-t border-zinc-200 dark:border-zinc-700 px-4 py-2 bg-white dark:bg-zinc-800">
                                <span
                                    class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mr-2">{{ __('Zoom') }}:</span>
                                <flux:button type="button" size="xs" variant="ghost"
                                    x-on:click="document.querySelector('cropper-image')?.$scale(-0.1)"
                                    title="{{ __('Reduzir zoom') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                </flux:button>
                                <flux:button type="button" size="xs" variant="ghost"
                                    x-on:click="document.querySelector('cropper-image')?.$scale(0.1)"
                                    title="{{ __('Aumentar zoom') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                </flux:button>
                                <span class="mx-2 text-zinc-300 dark:text-zinc-600">|</span>
                                <flux:button type="button" size="xs" variant="ghost"
                                    x-on:click="document.querySelector('cropper-image')?.$rotate(-90)"
                                    title="{{ __('Rotacionar 90°') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 4v6h6" />
                                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10" />
                                    </svg>
                                </flux:button>
                                <flux:button type="button" size="xs" variant="ghost"
                                    x-on:click="
                                        document.querySelector('cropper-image')?.$resetTransform();
                                        document.querySelector('cropper-selection')?.$reset();
                                    "
                                    title="{{ __('Redefinir') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                                        <path d="M3 3v5h5" />
                                    </svg>
                                </flux:button>
                            </div>

                            <div
                                class="flex items-center justify-between gap-2 border-t border-zinc-200 dark:border-zinc-700 px-4 py-3 bg-white dark:bg-zinc-800">
                                <flux:text class="text-xs">{{ __('Arraste para ajustar a área de corte.') }}
                                </flux:text>
                                <div class="flex items-center gap-2">
                                    <flux:button type="button" size="sm" variant="ghost"
                                        x-on:click="$wire.removeLogo()">
                                        {{ __('Cancelar') }}
                                    </flux:button>
                                    <flux:button type="button" size="sm" variant="primary"
                                        x-on:click="
                                            const sel = document.querySelector('cropper-selection');
                                            if (sel) {
                                                sel.$toCanvas({ width: 200, height: 200 }).then(function(canvas) {
                                                    const dataUrl = canvas.toDataURL('image/webp', 0.85);
                                                    $wire.set('cropped_logo', dataUrl);
                                                });
                                                cropApplied = true;
                                            }
                                        "
                                        x-bind:class="cropApplied ? 'opacity-50' : ''"
                                        x-text="cropApplied ? '{{ __('Corte aplicado') }}' : '{{ __('Aplicar corte') }}'">
                                        {{ __('Aplicar corte') }}
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Letter & gradient (só quando não tem imagem) --}}
                    <template x-if="!hasImage()">
                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>{{ __('Letra') }}</flux:label>
                                <flux:input x-model="logo_text" maxlength="5"
                                    class="w-20 text-center text-lg font-bold" />
                                <flux:error name="logo_text" />
                            </flux:field>

                            <div></div>

                            <flux:field>
                                <flux:label>{{ __('Cor gradiente — início') }}</flux:label>
                                <div class="flex gap-2">
                                    <flux:input type="color" x-model="logo_gradient_from_hex" class="w-12 p-1" />
                                    <flux:input x-model="logo_gradient_from" placeholder="red-500" class="flex-1" />
                                </div>
                                <flux:error name="logo_gradient_from" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Cor gradiente — fim') }}</flux:label>
                                <div class="flex gap-2">
                                    <flux:input type="color" x-model="logo_gradient_to_hex" class="w-12 p-1" />
                                    <flux:input x-model="logo_gradient_to" placeholder="orange-500" class="flex-1" />
                                </div>
                                <flux:error name="logo_gradient_to" />
                            </flux:field>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Animações --}}
            <div>
                <flux:heading size="lg">{{ __('Animações') }}</flux:heading>
                <flux:subheading>{{ __('Ative ou desative animações e transições no sistema.') }}</flux:subheading>

                <div class="mt-4">
                    <flux:switch x-model="ui_animations" :label="__('Animações ativas')" />
                </div>
            </div>

            {{-- Actions --}}
            <div
                class="flex items-center justify-between gap-4 border-t border-zinc-200 dark:border-zinc-700 pt-6 pb-4">
                <flux:button type="button" variant="ghost" wire:click="resetToDefaults"
                    wire:confirm="{{ __('Tem certeza? Isso restaurará os valores padrão.') }}">
                    {{ __('Restaurar padrões') }}
                </flux:button>

                <flux:button type="submit" variant="primary">
                    {{ __('Salvar') }}
                </flux:button>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
