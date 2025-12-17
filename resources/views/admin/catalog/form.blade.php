
@extends('admin.layouts.base')

@php($isEdit = $mode === 'edit')

@section('title', ($isEdit ? 'Edit' : 'Add') . ' Product | The Skool Store')
@section('page_heading', ($isEdit ? 'Edit' : 'Add') . ' Product')
@section('page_subheading', 'Curate listings with full catalog metadata')

@section('content')
    <div style="display:flex;justify-content:flex-end;align-items:center;margin-bottom:24px;">

        <a href="{{ route('master.admin.catalog.index') }}" class="btn-back-outline">
            <i class="fas fa-arrow-left"></i> Back to catalog
        </a>
    </div>

    <form method="POST" action="{{ $isEdit ? route('master.admin.catalog.update', $product) : route('master.admin.catalog.store') }}" enctype="multipart/form-data">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">
            <!-- Left Column -->
            <div style="display:flex;flex-direction:column;gap:24px;">
                
                <!-- Basic Info -->
                <div class="card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-info-circle" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Product Information
                    </h3>
                    <label style="margin-bottom:16px;">
                        <span>Product Name *</span>
                        <input type="text" name="product_name" value="{{ old('product_name', $product->product_name) }}" required>
                    </label>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <label>
                            <span>Description</span>
                            <textarea name="description" rows="5" placeholder="Rich text / marketing copy...">{{ old('description', $product->description) }}</textarea>
                        </label>
                        <label>
                            <span>Size Guidance</span>
                            <textarea name="size_guidance" rows="5" placeholder="Add measurement tips or conversion charts...">{{ old('size_guidance', $product->size_guidance) }}</textarea>
                        </label>
                    </div>
                    

                </div>

                <!-- Pricing -->
                <div class="card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-tag" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Pricing
                    </h3>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;">
                        <label>
                            <span>Regular price *</span>
                            <input type="number" name="price_regular" min="0" step="0.01" value="{{ old('price_regular', $product->price_regular) }}" required>
                        </label>
                        <label>
                            <span>Sale price</span>
                            <input type="number" name="price_sale" min="0" step="0.01" value="{{ old('price_sale', $product->price_sale) }}">
                        </label>
                        <label>
                            <span>Tax (%)</span>
                            <input type="number" name="price_tax" min="0" step="0.01" value="{{ old('price_tax', $product->price_tax) }}">
                        </label>
                        <label>
                            <span>Tax profile</span>
                            <select name="tax_profile">
                                <option value="">Select profile</option>
                                @foreach(['gst-5','gst-12','gst-18'] as $profile)
                                    <option value="{{ $profile }}" @selected(old('tax_profile', $product->tax_profile) === $profile)>{{ strtoupper($profile) }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </div>

                <!-- Metadata Hidden Inputs -->
                <input type="hidden" name="inventory_stock" value="{{ old('inventory_stock', $product->inventory_stock ?? 0) }}">
                <input type="hidden" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}">

                <!-- Variants (Size & Stock) -->
                <div class="card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-layer-group" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Product Variants
                    </h3>
                    <div id="variants-container">
                        @if(old('variants'))
                            @foreach(old('variants') as $index => $variant)
                                <div class="variant-row" style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:12px;margin-bottom:12px;">
                                    <label>
                                        <span style="font-size:12px;">Size / Option</span>
                                        <input type="text" name="variants[{{$index}}][option]" value="{{ $variant['option'] }}" placeholder="e.g. S, M, 10" required>
                                        <input type="hidden" name="variants[{{$index}}][id]" value="{{ $variant['id'] ?? '' }}">
                                    </label>
                                    <label>
                                        <span style="font-size:12px;">Stock</span>
                                        <input type="number" name="variants[{{$index}}][stock]" value="{{ $variant['stock'] }}" placeholder="Qty" min="0" class="variant-stock">
                                    </label>
                                    <label>
                                        <span style="font-size:12px;">Low Stock Alert</span>
                                        <input type="number" name="variants[{{$index}}][low_stock_threshold]" value="{{ $variant['low_stock_threshold'] ?? 5 }}" placeholder="Alert Qty" min="0">
                                    </label>
                                    <div style="display:flex;align-items:end;padding-bottom:10px;">
                                        <button type="button" class="btn-remove-variant" style="color:#b42318;background:none;border:none;cursor:pointer;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @elseif($product->variants && $product->variants->count() > 0)
                            @foreach($product->variants as $index => $variant)
                                <div class="variant-row" style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:12px;margin-bottom:12px;">
                                    <label>
                                        <span style="font-size:12px;">Size / Option</span>
                                        <input type="text" name="variants[{{$index}}][option]" value="{{ $variant->option }}" placeholder="e.g. S, M, 10" required>
                                        <input type="hidden" name="variants[{{$index}}][id]" value="{{ $variant->id }}">
                                    </label>
                                    <label>
                                        <span style="font-size:12px;">Stock</span>
                                        <input type="number" name="variants[{{$index}}][stock]" value="{{ $variant->stock }}" placeholder="Qty" min="0" class="variant-stock">
                                    </label>
                                    <label>
                                        <span style="font-size:12px;">Low Stock Alert</span>
                                        <input type="number" name="variants[{{$index}}][low_stock_threshold]" value="{{ $variant->low_stock_threshold }}" placeholder="Alert Qty" min="0">
                                    </label>
                                    <div style="display:flex;align-items:end;padding-bottom:10px;">
                                        <button type="button" class="btn-remove-variant" style="color:#b42318;background:none;border:none;cursor:pointer;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Empty State / One Default Row -->
                             <div class="variant-row" style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:12px;margin-bottom:12px;">
                                <label>
                                    <span style="font-size:12px;">Size / Option</span>
                                    <input type="text" name="variants[0][option]" placeholder="e.g. S, M, 10">
                                </label>
                                <label>
                                    <span style="font-size:12px;">Stock</span>
                                    <input type="number" name="variants[0][stock]" placeholder="Qty" min="0" class="variant-stock">
                                </label>
                                <label>
                                    <span style="font-size:12px;">Low Stock Alert</span>
                                    <input type="number" name="variants[0][low_stock_threshold]" placeholder="Alert Qty" min="0" value="5">
                                </label>
                                <div style="display:flex;align-items:end;padding-bottom:10px;">
                                    <button type="button" class="btn-remove-variant" style="color:#b42318;background:none;border:none;cursor:pointer;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button type="button" id="add-variant-btn" style="margin-top:10px;background:#f9fafb;border:1px dashed #d0d5dd;border-radius:8px;width:100%;padding:10px;color:#475467;font-size:13px;cursor:pointer;">
                        + Add another size/variant
                    </button>
                </div>

                <!-- Media -->
                <div class="card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-images" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Media
                    </h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <label>
                            <span>Featured product image</span>
                            <div class="file-input-wrapper">
                                <input type="file" name="featured_image" accept="image/*">
                            </div>
                            @if($product->featured_image)
                                <div style="margin-top:8px;">
                                    <img src="{{ Str::startsWith($product->featured_image, 'http') ? $product->featured_image : asset('storage/' . $product->featured_image) }}" alt="Featured" style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                                </div>
                            @endif
                        </label>
                        <div>
                            <span>Gallery images (Drag & Drop to Reorder)</span>
                            <div id="media-drop-zone" style="border: 2px dashed #d1d5db; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: border-color 0.3s;" onclick="document.getElementById('gallery-upload-input').click()">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 24px; color: #9ca3af; margin-bottom: 8px;"></i>
                                <p style="margin: 0; color: #6b7280;">Click or Drag files here to upload</p>
                                <input type="file" id="gallery-upload-input" multiple accept="image/*,video/*" style="display: none;">
                                <input type="hidden" name="media_list_modified" value="1">
                                <input type="hidden" name="media_order_ids" id="media_order_ids">
                            </div>
                            
                            <!-- Unified Media Grid -->
                            <div id="unifiedMediaPreview" style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 16px;">
                                <!-- Existing images rendered as media items -->
                                @if($product->media_images)
                                    @foreach($product->media_images as $index => $img)
                                        <div class="media-item existing-media" draggable="true">
                                            <img src="{{ Str::startsWith($img, 'http') ? $img : asset('storage/' . $img) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            <div class="position-number">{{ $loop->iteration }}</div>
                                            <div class="remove-btn" onclick="this.parentElement.remove(); updatePositionNumbers();">
                                                <i class="fas fa-times"></i>
                                            </div>
                                            <input type="hidden" name="existing_media_images[]" value="{{ $img }}">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        /* Media Item Container */
        .media-item {
            position: relative;
            width: 120px;
            height: 120px;
            border: 2px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            cursor: grab;
            transition: all 0.3s ease;
            user-select: none;
            background: white;
        }
        .media-item:active { cursor: grabbing; }
        .media-item.dragging {
            opacity: 0.7;
            transform: rotate(3deg) scale(1.05);
            z-index: 1000;
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
        }
        .media-item.drag-over { border: 2px dashed #007bff; transform: scale(1.02); }
        /* Overlays */
        .position-number {
            position: absolute; bottom: 8px; left: 8px;
            width: 28px; height: 28px; background: rgba(0, 123, 255, 0.9);
            color: white; border-radius: 50%; font-weight: bold;
            display: flex; align-items: center; justify-content: center;
            pointer-events: none;
            font-size: 12px;
            z-index: 2;
        }
        .remove-btn {
            position: absolute; top: 8px; right: 8px;
            width: 24px; height: 24px; background: rgba(220, 53, 69, 0.9);
            color: white; border-radius: 50%; cursor: pointer;
            display: none; align-items: center; justify-content: center;
            z-index: 2;
            font-size: 12px;
        }
        .media-item:hover .remove-btn { display: flex; }
        .video-thumbnail {
            width: 100%; height: 100%; background: #000; display: flex; align-items: center; justify-content: center;
        }
        .video-play-button {
            width: 40px; height: 40px; background: rgba(255,255,255,0.2);
            border-radius: 50%; color: white; display: flex;
            align-items: center; justify-content: center; font-size: 16px;
            backdrop-filter: blur(2px);
             transition: background 0.2s;
        }
         .video-thumbnail:hover .video-play-button { background: rgba(255,255,255,0.4); }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.getElementById('media-drop-zone');
            const fileInput = document.getElementById('gallery-upload-input');
            const previewContainer = document.getElementById('unifiedMediaPreview');
            let draggedItem = null;

            // Make available globally for HTML onclick handlers
            window.updatePositionNumbers = function() {
                const items = previewContainer.querySelectorAll('.media-item');
                items.forEach((item, index) => {
                    const badge = item.querySelector('.position-number');
                    if(badge) badge.textContent = index + 1;
                });
            }

            // Handle File Selection
            fileInput.addEventListener('change', (e) => handleFiles(e.target.files));

            // Drag & Drop Zone Events
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = '#490d59';
                dropZone.style.background = '#f7f2fb';
            });
            dropZone.addEventListener('dragleave', (e) => {
                 e.preventDefault();
                 dropZone.style.borderColor = '#d1d5db';
                 dropZone.style.background = '';
            });
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = '#d1d5db';
                dropZone.style.background = '';
                handleFiles(e.dataTransfer.files);
            });

            function handleFiles(files) {
                Array.from(files).forEach(file => createUploadedMediaPreview(file));
                updatePositionNumbers();
            }

            window.createUploadedMediaPreview = function(file) {
                const mediaContainer = document.createElement('div');
                mediaContainer.className = 'media-item';
                mediaContainer.draggable = true; // Enable standard drag API

                // Content Type Logic
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    mediaContainer.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const thumb = document.createElement('div');
                    thumb.className = 'video-thumbnail';
                    thumb.innerHTML = `<div class="video-play-button"><i class="fas fa-play"></i></div>`;
                    thumb.onclick = (e) => {
                         e.stopPropagation(); // Prevent drag start interference
                         playVideo(file, thumb);
                    };
                    mediaContainer.appendChild(thumb);
                }

                // Interaction Elements
                const posBadge = document.createElement('div');
                posBadge.className = 'position-number';
                mediaContainer.appendChild(posBadge);

                const removeBtn = document.createElement('div');
                removeBtn.className = 'remove-btn';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.onclick = (e) => {
                    e.stopPropagation();
                    mediaContainer.remove();
                    updatePositionNumbers();
                };
                mediaContainer.appendChild(removeBtn);

                // Hidden Input for Form Submission
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'file';
                hiddenInput.name = 'media_images[]'; // Correct array name for backend
                hiddenInput.className = 'd-none';
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                hiddenInput.files = dataTransfer.files;
                mediaContainer.appendChild(hiddenInput);

                // Add Drag Events
                addDragEvents(mediaContainer);

                previewContainer.appendChild(mediaContainer);
            };

            function addDragEvents(item) {
                // Desktop Mouse Dragging
                item.addEventListener('mousedown', (e) => {
                    if(e.target.closest('.remove-btn') || e.target.closest('.video-play-button')) return;
                    // Basic drag start logic can go here for custom visual if needed, 
                    // but we will rely on standard HTML5 DragStart for simplicity with the 'draggable' attr.
                });

                // HTML5 Drag API
                item.addEventListener('dragstart', (e) => {
                     draggedItem = item;
                     setTimeout(() => item.classList.add('dragging'), 0);
                });
                item.addEventListener('dragend', () => {
                    draggedItem = null;
                    item.classList.remove('dragging');
                    updatePositionNumbers();
                });
                item.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    if (item !== draggedItem) {
                        const bounding = item.getBoundingClientRect();
                        const offset = bounding.x + bounding.width / 2;
                        if (e.clientX - offset > 0) {
                            item.style.borderRight = '2px solid #007bff';
                            item.style.borderLeft = '';
                        } else {
                            item.style.borderLeft = '2px solid #007bff';
                            item.style.borderRight = '';
                        }
                    }
                });
                 item.addEventListener('dragleave', () => {
                    item.style.borderLeft = '';
                    item.style.borderRight = '';
                });
                item.addEventListener('drop', (e) => {
                    e.preventDefault();
                    item.style.borderLeft = '';
                    item.style.borderRight = '';
                    if (draggedItem && draggedItem !== item) {
                         const bounding = item.getBoundingClientRect();
                         const offset = bounding.x + bounding.width / 2;
                         if (e.clientX - offset > 0) {
                             item.after(draggedItem);
                         } else {
                             item.before(draggedItem);
                         }
                    }
                });
            }

            function playVideo(file, container) {
                const video = document.createElement('video');
                video.controls = true;
                video.style.width = '100%';
                video.style.height = '100%';
                
                if (file.name.endsWith('.m3u8') && Hls.isSupported()) {
                    const hls = new Hls();
                    hls.loadSource(URL.createObjectURL(file));
                    hls.attachMedia(video);
                } else {
                    video.src = URL.createObjectURL(file);
                }
                
                container.innerHTML = '';
                container.appendChild(video);
                video.play();
            }

            // Initialize drag events for existing items
            document.querySelectorAll('.existing-media').forEach(item => {
                addDragEvents(item);
            });
        });
    </script>
    @endpush
                    
                    <div style="margin-top:16px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <label>
                            <span>Size Chart (Image)</span>
                            <div class="file-input-wrapper">
                                <input type="file" name="size_chart_path" accept="image/*">
                            </div>
                            @if($product->size_chart_path)
                                <div style="margin-top:8px;">
                                    <a href="{{ asset('storage/' . $product->size_chart_path) }}" target="_blank" style="font-size:12px;color:#490d59;">View current chart</a>
                                </div>
                            @endif
                        </label>
                        <label>
                            <span>Measurement Video (YouTube URL)</span>
                            <input type="url" name="video_url" value="{{ old('video_url', $product->video_url) }}" placeholder="https://youtube.com/watch?v=...">
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right Column (Sidebar Style) -->
            <div style="display:flex;flex-direction:column;gap:24px;">
                <div class="card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-sliders-h" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Organization
                    </h3>
                    
                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <label>
                            <span>School *</span>
                            <select name="school_id" required>
                                <option value="">Select school</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}" @selected(old('school_id', $product->school_id) == $school->id)>{{ $school->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Grade</span>
                            <select name="grade">
                                <option value="">All grades</option>
                                @foreach($grades as $key => $label)
                                    <option value="{{ $key }}" @selected(old('grade', $product->grade) == $key)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Category</span>
                            <select name="category">
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" @selected(old('category', $product->category) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Product Type</span>
                            <select name="product_type">
                                <option value="">Select Type</option>
                                @foreach($productTypes as $key => $label)
                                    <option value="{{ $key }}" @selected(old('product_type', $product->product_type) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Gender *</span>
                            <select name="gender" required>
                                @foreach(['male' => 'Male', 'female' => 'Female', 'unisex' => 'Unisex'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('gender', $product->gender) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Tag name</span>
                            <input type="text" name="tag_name" value="{{ old('tag_name', $product->tag_name) }}" placeholder="Eg: Bestseller">
                        </label>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-check-circle" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Publish
                    </h3>
                    <label style="margin-bottom:16px;">
                        <span>Stock status *</span>
                        <select name="stock_status">
                            @foreach(['in_stock' => 'In stock','out_of_stock' => 'Out of stock'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('stock_status', $product->stock_status ?? 'in_stock') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label style="margin-bottom:20px;">
                        <span>Availability label</span>
                        <input type="text" name="availability_label" value="{{ old('availability_label', $product->availability_label) }}" placeholder="Eg: Ships in 2-3 days">
                    </label>

                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <button type="submit" name="status" value="live" style="width:100%;padding:12px;border:none;border-radius:8px;background:#490d59;color:#fff;font-weight:600;cursor:pointer;">
                            Publish Product
                        </button>
                        <button type="submit" name="status" value="draft" style="width:100%;padding:12px;border-radius:8px;border:1px solid #d0d5dd;background:#fff;color:#475467;cursor:pointer;">
                            Save Draft
                        </button>
                        @if($isEdit)
                            <button type="submit" name="status" value="archived" style="width:100%;padding:12px;border-radius:8px;border:1px solid #d0d5dd;background:#fff;color:#b42318;cursor:pointer;">
                                Archive Product
                            </button>
                        @endif
                        <a href="{{ route('master.admin.catalog.index') }}" style="text-align:center;padding:12px;color:#475467;text-decoration:none;">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('variants-container');
            const addBtn = document.getElementById('add-variant-btn');
            const mainStockInput = document.querySelector('input[name="inventory_stock"]');

            function updateMainStock() {
                const stockInputs = container.querySelectorAll('input[name*="[stock]"]');
                let totalStock = 0;
                if (stockInputs.length > 0) {
                    stockInputs.forEach(input => {
                        totalStock += parseInt(input.value) || 0;
                    });
                }
                if (mainStockInput) {
                    mainStockInput.value = totalStock;
                }
            }

            // Initial check
            updateMainStock();

            addBtn.addEventListener('click', function() {
                const index = container.querySelectorAll('.variant-row').length;
                const row = document.createElement('div');
                row.className = 'variant-row';
                row.style.cssText = 'display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:12px;margin-bottom:12px;';
                row.innerHTML = `
                    <label>
                        <span style="font-size:12px;">Size / Option</span>
                        <input type="text" name="variants[${index}][option]" placeholder="e.g. S, M, 10" required>
                    </label>
                    <label>
                        <span style="font-size:12px;">Stock</span>
                        <input type="number" name="variants[${index}][stock]" placeholder="Qty" min="0" class="variant-stock">
                    </label>
                    <label>
                        <span style="font-size:12px;">Low Stock Alert</span>
                        <input type="number" name="variants[${index}][low_stock_threshold]" placeholder="Alert Qty" min="0" value="5">
                    </label>
                    <div style="display:flex;align-items:end;padding-bottom:10px;">
                        <button type="button" class="btn-remove-variant" style="color:#b42318;background:none;border:none;cursor:pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                container.appendChild(row);
                updateMainStock();
            });

            container.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-variant')) {
                    e.target.closest('.variant-row').remove();
                    updateMainStock();
                }
            });

            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('variant-stock')) {
                    updateMainStock();
                }
            });
        });
    </script>
@endsection