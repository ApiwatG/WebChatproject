{{-- resources/views/components/character-preview.blade.php --}}

@props(['user', 'size' => 'medium', 'showEquipped' => false])

@php
    $sizes = [
        'small' => ['container' => '200px', 'height' => '250px'],
        'medium' => ['container' => '300px', 'height' => '400px'],
        'large' => ['container' => '400px', 'height' => '500px'],
    ];
    
    $dimensions = $sizes[$size] ?? $sizes['medium'];
    $equippedCosmetics = $user->equippedCosmetics()->with(['cosmeticType', 'rarity'])->get();
@endphp

<style>
    .character-preview-{{ $size }} {
        width: {{ $dimensions['container'] }};
    }

    .character-display-{{ $size }} {
        position: relative;
        width: 100%;
        height: {{ $dimensions['height'] }};
        overflow: hidden;
    }

    /* Character base layer */
    .character-base-{{ $size }} {
        position: absolute;
        bottom: 0;
        left: 40%;
        transform: translateX(-50%) scaleX(-1);
        height: 85%;
        width: auto;
        object-fit: contain;
        z-index: 1;
    }

    /* Cosmetic layers */
    .cosmetic-layer-{{ $size }} {
        position: absolute;
        object-fit: contain;
        transition: opacity 0.3s;
        pointer-events: none;
    }

    /* Positioning for different cosmetic types */
    .cosmetic-layer-{{ $size }}.hat {
        top: -30%;
        left: 57%;
        transform: translateX(-50%);
        width: 160%;
        height: auto;
        z-index: 3;
    }

    .cosmetic-layer-{{ $size }}.outfit {
        top: 28%;
        left: 50%;
        transform: translateX(-50%);
        width: 50%;
        height: auto;
        z-index: 2;
    }

    .cosmetic-layer-{{ $size }}.accessory {
        top: 32%;
        left: 50%;
        transform: translateX(-50%);
        width: 40%;
        height: auto;
        z-index: 4;
    }
</style>

<div class="character-preview-{{ $size }}">
    <div class="character-display-{{ $size }}">
        <!-- Character Base -->
        <img 
            src="{{ asset('css/img/character-base.png') }}" 
            alt="Character" 
            class="character-base-{{ $size }}"
            onerror="this.style.display='none';"
        >
        
        <!-- Equipped Cosmetics Overlay -->
        @foreach($equippedCosmetics as $cosmetic)
            <img 
                src="{{ $cosmetic->image_url }}" 
                alt="{{ $cosmetic->cosmetic_name }}"
                class="cosmetic-layer-{{ $size }} {{ strtolower($cosmetic->cosmeticType->cosmetictype_name) }}"
                data-type="{{ strtolower($cosmetic->cosmeticType->cosmetictype_name) }}"
                onerror="this.style.display='none';"
            >
        @endforeach
    </div>
</div>