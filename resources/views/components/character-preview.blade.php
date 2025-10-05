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
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .character-preview-{{ $size }} h3 {
        text-align: center;
        margin-bottom: 15px;
        color: #333;
        font-size: {{ $size === 'small' ? '14px' : '18px' }};
    }

    .character-display-{{ $size }} {
        position: relative;
        width: 100%;
        height: {{ $dimensions['height'] }};
        border-radius: 12px;
        overflow: hidden;
    }


    /* Character base layer */
    .character-base-{{ $size }} {
        position: absolute;
        bottom: 0;
        left: -20%;
        transform: translateX(-50%);
        height: 85%;
        width: auto;
        object-fit: contain;
        z-index: 1;
        transform: scaleX(-1);
    
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

   

    .equipped-list-{{ $size }} {
        margin-top: 15px;
        padding: 12px;
        background: #f9f9f9;
        border-radius: 8px;
        max-height: 200px;
        overflow-y: auto;
    }

    .equipped-list-{{ $size }} h4 {
        font-size: 12px;
        color: #666;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .equipped-item-{{ $size }} {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px;
        background: white;
        border-radius: 6px;
        margin-bottom: 6px;
    }

    .equipped-item-img-{{ $size }} {
        width: 30px;
        height: 30px;
        object-fit: cover;
        border-radius: 4px;
    }

    .equipped-item-name-{{ $size }} {
        flex: 1;
        font-size: 12px;
        font-weight: 500;
    }

    .equipped-item-type-{{ $size }} {
        font-size: 10px;
        color: #999;
    }
</style>

<div class="character-preview-{{ $size }}">
    <h3>{{ $user->name }}'s Character</h3>
    
    <div class="character-display-{{ $size }}">
        <!-- Background Scene -->
        <img 
            src="{{ asset('css/img/character-scene-bg.png') }}" 
            alt="Background" 
            class="character-background-{{ $size }}"
            onerror="this.style.display='none'; this.parentElement.style.background='linear-gradient(180deg, #a78bfa, #c084fc)';"
        >
        
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

    @if($showEquipped && $equippedCosmetics->count() > 0)
    <div class="equipped-list-{{ $size }}">
        <h4>Equipped Items:</h4>
        @foreach($equippedCosmetics as $cosmetic)
            <div class="equipped-item-{{ $size }}">
                <img 
                    src="{{ $cosmetic->image_url }}" 
                    class="equipped-item-img-{{ $size }}" 
                    alt="{{ $cosmetic->cosmetic_name }}"
                    onerror="this.style.display='none';"
                >
                <div>
                    <div class="equipped-item-name-{{ $size }}">{{ $cosmetic->cosmetic_name }}</div>
                    <div class="equipped-item-type-{{ $size }}">{{ $cosmetic->cosmeticType->cosmetictype_name }}</div>
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>