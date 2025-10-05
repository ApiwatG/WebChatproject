<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/buycosmetic.css') }}" />
  <title>Shop - The Twilight Bar</title>
</head>
<body>
  <div class="cosmetic-container">
    <div class="cosmetic-panel">
      <div>
        <h1>BUY COSMETIC</h1>
        <div class="coin-display">🪙 {{ $user->coins }} coins</div>
        
        <div class="input-group mb-3">
          <input
            type="text"
            class="form-control"
            id="searchInput"
            placeholder="Search cosmetic..."
            value="{{ request('search') }}"
          />
        </div>
        
        <div class="filter-section">
          <select id="rarityFilter">
            <option value="">All Rarities</option>
            @foreach($rarities as $rarity)
              <option value="{{ $rarity->id }}" {{ request('rarity') == $rarity->id ? 'selected' : '' }}>
                {{ $rarity->rarity_name }}
              </option>
            @endforeach
          </select>
          
          <select id="typeFilter">
            <option value="">All Types</option>
            @foreach($types as $type)
              <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                {{ $type->cosmetictype_name }}
              </option>
            @endforeach
          </select>
        </div>
        
        <div class="cosmetic-grid">
          @forelse($cosmetics as $cosmetic)
            <div class="cosmetic-card">
              @if(in_array($cosmetic->id, $ownedCosmeticIds))
                <span class="owned-badge">✓ Owned</span>
              @endif
              
              <img src="{{ $cosmetic->image_url }}" alt="{{ $cosmetic->cosmetic_name }}" class="cosmetic-img">
              
              <div class="cosmetic-name">{{ $cosmetic->cosmetic_name }}</div>
              
              <span class="cosmetic-rarity rarity-{{ strtolower($cosmetic->rarity->rarity_name) }}">
                {{ $cosmetic->rarity->rarity_name }}
              </span>
              
              <div class="cosmetic-price">🪙 {{ $cosmetic->price }}</div>
              
              @if(in_array($cosmetic->id, $ownedCosmeticIds))
                <button class="btn-buy" disabled>Already Owned</button>
              @else
                <button class="btn-buy" onclick="purchaseCosmetic({{ $cosmetic->id }}, {{ $cosmetic->price }})">
                  Buy Now
                </button>
              @endif
            </div>
          @empty
            <div class="empty-state">
              <p>No cosmetics found</p>
            </div>
          @endforelse
        </div>
      </div>
      
      <div style="margin-top: 20px;">
        <a href="{{ route('dashboard') }}"><button class="btn-cancel">Back</button></a>
      </div>
    </div>
  </div>

  <script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
      filterCosmetics();
    });
    
    document.getElementById('rarityFilter').addEventListener('change', function() {
      filterCosmetics();
    });
    
    document.getElementById('typeFilter').addEventListener('change', function() {
      filterCosmetics();
    });
    
    function filterCosmetics() {
      const search = document.getElementById('searchInput').value;
      const rarity = document.getElementById('rarityFilter').value;
      const type = document.getElementById('typeFilter').value;
      
      const params = new URLSearchParams();
      if (search) params.append('search', search);
      if (rarity) params.append('rarity', rarity);
      if (type) params.append('type', type);
      
      window.location.href = '{{ route("shop.index") }}?' + params.toString();
    }
    
    function purchaseCosmetic(cosmeticId, price) {
      const userCoins = {{ $user->coins }};
      
      if (userCoins < price) {
        alert('Not enough coins!');
        return;
      }
      
      if (!confirm(`Purchase this cosmetic for ${price} coins?`)) {
        return;
      }
      
      fetch(`/shop/${cosmeticId}/purchase`, {
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
          alert(data.message);
          window.location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Failed to purchase cosmetic');
      });
    }
  </script>
</body>
</html>