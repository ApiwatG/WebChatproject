<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/cosmetic.css') }}">
  <title>My Cosmetics - The Twilight Bar</title>
  
</head>
<body>
  <div class="cosmetic-container"> 
    <div class="cosmetic-panel">
      <div>
        <h1>MY COSMETICS</h1>
        
        <div class="input-group mb-3">     
          <input 
            type="text" 
            class="form-control" 
            id="searchInput"
            placeholder="Search cosmetic..."
            value="{{ request('search') }}"
          >
        </div>
        
        <div class="filter-section">
          <select id="typeFilter">
            <option value="">All Types</option>
            @foreach($cosmeticTypes as $type)
              <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                {{ $type->cosmetictype_name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="cosmetic-grid">
          @forelse($cosmetics as $cosmetic)
            <div class="cosmetic-card {{ $cosmetic->pivot->is_equipped ? 'equipped' : '' }}" 
                 id="cosmetic-{{ $cosmetic->id }}">
              @if($cosmetic->pivot->is_equipped)
                <span class="equipped-badge">⭐ Equipped</span>
              @endif
              
              <img src="{{ $cosmetic->image_url }}" 
                   alt="{{ $cosmetic->cosmetic_name }}" 
                   class="cosmetic-img">
              
              <div class="cosmetic-name">{{ $cosmetic->cosmetic_name }}</div>
              
              <span class="cosmetic-rarity rarity-{{ strtolower($cosmetic->rarity->rarity_name) }}">
                {{ $cosmetic->rarity->rarity_name }}
              </span>
              
              <div style="font-size: 12px; color: #666; margin-top: 5px;">
                {{ $cosmetic->cosmeticType->cosmetictype_name }}
              </div>
              
              @if($cosmetic->pivot->is_equipped)
                <button class="btn-equip equipped" 
                        onclick="unequipCosmetic({{ $cosmetic->id }})">
                  Unequip
                </button>
              @else
                <button class="btn-equip" 
                        onclick="equipCosmetic({{ $cosmetic->id }})">
                  Equip
                </button>
              @endif
            </div>
          @empty
            <div class="empty-state">
              <p>You don't have any cosmetics yet!</p>
              <p><a href="{{ route('shop.index') }}">Visit the shop to buy some</a></p>
            </div>
          @endforelse
        </div>
      </div>
      
      <div style="margin-top: 20px;">      
        <a href="{{ route('dashboard') }}"><button class="btn-cancle">Back</button></a>
      </div>
    </div>
  </div>

  <script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
      filterCosmetics();
    });
    
    document.getElementById('typeFilter').addEventListener('change', function() {
      filterCosmetics();
    });
    
    function filterCosmetics() {
      const search = document.getElementById('searchInput').value;
      const type = document.getElementById('typeFilter').value;
      
      const params = new URLSearchParams();
      if (search) params.append('search', search);
      if (type) params.append('type', type);
      
      window.location.href = '{{ route("cosmetic.index") }}?' + params.toString();
    }
    
    function equipCosmetic(cosmeticId) {
      fetch(`/cosmetic/${cosmeticId}/equip`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          window.location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Failed to equip cosmetic');
      });
    }
    
    function unequipCosmetic(cosmeticId) {
      fetch(`/cosmetic/${cosmeticId}/unequip`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          window.location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Failed to unequip cosmetic');
      });
    }