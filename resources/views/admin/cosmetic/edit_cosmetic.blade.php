@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="{{ asset('/css/cosmetic.css') }}">

<div class="page-header">
    <h1>Edit Cosmetic</h1>
    <a href="{{ route('admin.cosmetics.index') }}" class="back-btn">← Back to Cosmetics</a>
</div>

<div class="form-card">
    <h2>Edit Cosmetic Details</h2>

    <form method="POST" action="{{ route('admin.cosmetics.update', $cosmetic->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $cosmetic->cosmetic_name) }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Enter description">{{ old('description', $cosmetic->description) }}</textarea>
        </div>

        <div class="form-group">
            <label for="price">Price (Coins) *</label>
            <input type="number" id="price" name="price" min="0" step="1" value="{{ old('price', $cosmetic->price) }}" required>
        </div>

        <div class="form-group">
            <label for="cosmetic_type_id">Category *</label>
            <select id="cosmetic_type_id" name="cosmetic_type_id" required>
                <option value="">Select Category</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ $cosmetic->cosmetic_type_id == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="rarity_id">Rarity *</label>
            <select id="rarity_id" name="rarity_id" required>
                <option value="">Select Rarity</option>
                @foreach($rarities as $rarity)
                    <option value="{{ $rarity->id }}" {{ $cosmetic->rarity_id == $rarity->id ? 'selected' : '' }}>
                        {{ $rarity->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Current Image</label><br>
            @if($cosmetic->cosmetic_img)
                <img src="{{ asset('storage/' . $cosmetic->cosmetic_img) }}" 
                     alt="{{ $cosmetic->cosmetic_name }}" 
                     style="width: 100px; height: 100px; border-radius: 6px; object-fit: cover;">
            @else
                <img src="https://via.placeholder.com/100?text=No+Image" alt="No image">
            @endif
        </div>

        <div class="form-group">
            <label for="image">New Image (optional)</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <button type="submit" class="submit-btn">Save Changes</button>
    </form>
</div>
@endsection
