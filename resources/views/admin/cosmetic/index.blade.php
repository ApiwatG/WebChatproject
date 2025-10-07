@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="{{ asset('/css/cosmetic.css') }}">

<div class="page-header">
    <h1>Manage Cosmetics</h1>
</div>

<div class="two-column-layout">
    <!-- Cosmetics List -->
    <div class="cosmetics-list">
        <h2>Existing Cosmetics</h2>
        
        @if($cosmetics->count() > 0)
            @foreach($cosmetics as $cosmetic)
            <div class="cosmetic-item">
                @if($cosmetic->cosmetic_img)
                    <img src="{{ asset('storage/' . $cosmetic->cosmetic_img) }}" 
                        alt="{{ $cosmetic->cosmetic_name }}" 
                        class="cosmetic-image"
                        onerror="this.onerror=null; this.src=&quot;https://via.placeholder.com/80?text=No+Image&quot;">
                @else
                    <img src="https://via.placeholder.com/80?text=No+Image" 
                         alt="{{ $cosmetic->cosmetic_name }}" 
                         class="cosmetic-image">
                @endif
                
                <div class="cosmetic-details">
                    <h3>{{ $cosmetic->cosmetic_name }}</h3>
                    <div class="cosmetic-meta">
                        <span class="badge badge-type">{{ $cosmetic->types->name }}</span>
                        <span class="badge badge-price">{{ number_format($cosmetic->price) }} coins</span>
                        <span class="badge" style="background: {{ $cosmetic->rarity->color ?? '#e2e8f0' }}; color: white;">
                            {{ $cosmetic->rarity->name }}
                        </span>
                    </div>
                    @if($cosmetic->description)
                        <p class="cosmetic-description">{{ $cosmetic->description }}</p>
                    @endif
                </div>
                
                <div class="cosmetic-actions">
                    {{-- ✅ เปลี่ยนปุ่ม Edit ให้ลิงก์ไปหน้าแก้ไข --}}
                    <a href="{{ route('admin.cosmetics.edit', $cosmetic->id) }}" class="btn-edit">Edit</a>

                    
                    <form method="POST" action="{{ route('admin.cosmetics.destroy', $cosmetic->id) }}" onsubmit="return confirm('Are you sure you want to delete this cosmetic?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete">Delete</button>
                    </form>
                </div>
            </div>
            @endforeach
        @else
            <div class="empty-state">
                <div class="empty-state-icon"></div>
                <h3>No Cosmetics Yet</h3>
                <p>Start by adding your first cosmetic item!</p>
            </div>
        @endif
    </div>
    
    <!-- Add Cosmetic Form -->
    <div class="form-card">
        <h2>Add New Cosmetic</h2>
        
        <form method="POST" action="{{ route('admin.cosmetics.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" required placeholder="Enter cosmetic name">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Enter description (optional)"></textarea>
            </div>
            
            <div class="form-group">
                <label for="price">Price (Coins) *</label>
                <input type="number" id="price" name="price" min="0" step="1" required placeholder="0">
            </div>
            
            <div class="form-group">
                <label for="cosmetic_type_id">Category *</label>
                <select id="cosmetic_type_id" name="cosmetic_type_id" required>
                    <option value="">Select Category</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="rarity_id">Rarity *</label>
                <select id="rarity_id" name="rarity_id" required>
                    <option value="">Select Rarity</option>
                    @foreach($rarities as $rarity)
                        <option value="{{ $rarity->id }}">{{ $rarity->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label>Image *</label>
                <div class="file-input-wrapper">
                    <input type="file" id="image" name="image" accept="image/*" required onchange="updateFileName(this)">
                </div>
            </div>
            
            <button type="submit" class="submit-btn">Add Item</button>
        </form>
    </div>
</div>

@endsection

