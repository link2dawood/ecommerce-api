@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Settings</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">General Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="site_name" class="form-label">Site Name</label>
                            <input type="text" class="form-control @error('site_name') is-invalid @enderror" 
                                   id="site_name" name="site_name" 
                                   value="{{ old('site_name', $settings['site_name'] ?? config('app.name')) }}">
                            @error('site_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="site_email" class="form-label">Site Email</label>
                            <input type="email" class="form-control @error('site_email') is-invalid @enderror" 
                                   id="site_email" name="site_email" 
                                   value="{{ old('site_email', $settings['site_email'] ?? '') }}">
                            @error('site_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="site_phone" class="form-label">Site Phone</label>
                            <input type="text" class="form-control @error('site_phone') is-invalid @enderror" 
                                   id="site_phone" name="site_phone" 
                                   value="{{ old('site_phone', $settings['site_phone'] ?? '') }}">
                            @error('site_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="site_address" class="form-label">Site Address</label>
                            <textarea class="form-control @error('site_address') is-invalid @enderror" 
                                      id="site_address" name="site_address" rows="3">{{ old('site_address', $settings['site_address'] ?? '') }}</textarea>
                            @error('site_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="site_description" class="form-label">Site Description</label>
                            <textarea class="form-control @error('site_description') is-invalid @enderror" 
                                      id="site_description" name="site_description" rows="3">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                            @error('site_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="currency" class="form-label">Currency</label>
                            <select class="form-select @error('currency') is-invalid @enderror" 
                                    id="currency" name="currency">
                                <option value="USD" {{ ($settings['currency'] ?? 'PKR') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                <option value="EUR" {{ ($settings['currency'] ?? 'PKR') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                <option value="GBP" {{ ($settings['currency'] ?? 'PKR') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                <option value="PKR" {{ ($settings['currency'] ?? 'PKR') == 'PKR' ? 'selected' : '' }}>PKR (₨)</option>
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Social Media Links</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="facebook_url" class="form-label">
                                <i class="fab fa-facebook text-primary"></i> Facebook URL
                            </label>
                            <input type="url" class="form-control" id="facebook_url" name="facebook_url"
                                   value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}"
                                   placeholder="https://facebook.com/yourpage">
                        </div>

                        <div class="mb-3">
                            <label for="twitter_url" class="form-label">
                                <i class="fab fa-twitter text-info"></i> Twitter URL
                            </label>
                            <input type="url" class="form-control" id="twitter_url" name="twitter_url"
                                   value="{{ old('twitter_url', $settings['twitter_url'] ?? '') }}"
                                   placeholder="https://twitter.com/yourhandle">
                        </div>

                        <div class="mb-3">
                            <label for="instagram_url" class="form-label">
                                <i class="fab fa-instagram text-danger"></i> Instagram URL
                            </label>
                            <input type="url" class="form-control" id="instagram_url" name="instagram_url"
                                   value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}"
                                   placeholder="https://instagram.com/yourprofile">
                        </div>

                        <div class="mb-3">
                            <label for="linkedin_url" class="form-label">
                                <i class="fab fa-linkedin text-primary"></i> LinkedIn URL
                            </label>
                            <input type="url" class="form-control" id="linkedin_url" name="linkedin_url"
                                   value="{{ old('linkedin_url', $settings['linkedin_url'] ?? '') }}"
                                   placeholder="https://linkedin.com/company/yourcompany">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Social Links
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Maintenance Mode</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="maintenance_mode" 
                                   name="maintenance_mode" value="1"
                                   {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="maintenance_mode">
                                Enable Maintenance Mode
                            </label>
                        </div>

                        <p class="text-muted small">
                            <i class="fas fa-info-circle"></i> When enabled, only admins can access the site.
                        </p>

                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fas fa-sync"></i> Update
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Cache Management</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Clear application cache to see latest changes.</p>
                    <form action="{{ route('admin.settings.clear-cache') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash-alt"></i> Clear Cache
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection