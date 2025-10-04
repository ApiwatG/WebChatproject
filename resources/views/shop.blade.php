<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/buycosmetic.css') }}" />
  <title>Shop - The Twilight Bar</title>
  <style>
    .cosmetic-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 20px;
      margin-top: 20px;
      max-height: 60vh;
      overflow-y: auto;
      padding: 10px;
    }
    
    .cosmetic-card {
      background: white;
      border-radius: 12px;
      padding: 15px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.2s;
      position: relative;
    }
    
    .cosmetic-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    
    .cosmetic-img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 10px;
      background: #f0f0f0;
    }
    
    .cosmetic-name {
      font-weight: 600;
      font-size: 16px;
      margin-bottom: 8px;
      color: #333;
    }
    
    .cosmetic-rarity {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      margin-bottom: 8px;
    }
    
    .rarity-common { background: #9ca3af; color: white; }
    .rarity-uncommon { background: #22c55e; color: white; }
    .rarity-rare { background: #3b82f6; color: white; }
    .rarity-epic { background: #a855f7; color: white; }
    .rarity-legendary { background: #f59e0b; color: white; }
    
    .cosmetic-price {
      font-size: 18px;
      font-weight: bold;
      color: #7b3bd6;
      margin-bottom: 10px;
    }
    
    .btn-buy {
      width: 100%;
      padding: 8px;
      background: linear-gradient(90deg, #b84be0, #7b3bd6);
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: opacity 0.2s;
    }
    
    .btn-buy:hover:not(:disabled) {
      opacity: 0.9;
    }
    
    .btn-buy:disabled {
      background: #d1d5db;
      cursor: not-allowed;
    }
    
    .owned-badge {
      position: absolute;
      top: 10px;
      right: 10px;
      background: #22c55e;
      color: white;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
    }
    
    .filter-section {
      display: flex;
      gap: 10px;
      margin-top: 15px;
      flex-wrap: wrap;
    }
    
    .filter-section select {
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid #e5e5e5;
      font-size: 14px;
    }
    
    .coin-display {
      display: inline-block;
      background: linear-gradient(90deg, #fbbf24, #f59e0b);
      color: white;
      padding: 8px 16px;
      border-radius: 20px;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .empty-state {
      text-align: center;
      padding: 40px;
      color: #999;
    }
  </style>
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