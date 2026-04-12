@extends('layouts.admin')

@section('title', 'Kelola Produk')
@section('page_heading', 'Produk undangan')

@php $adminActive = 'products'; @endphp

@section('content')
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
        <div class="text-muted small">Kelola template &amp; harga</div>
        <button class="btn btn-sm btn-gradient rounded-pill" data-bs-toggle="modal" data-bs-target="#productModal" data-mode="create">
            <i class="bi bi-plus-lg me-1"></i> Tambah produk
        </button>
    </div>

    <div class="card border-0 glass">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 js-datatable" data-page-length="10">
                    <thead class="table-light">
                        <tr>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        @forelse ($products as $product)
                            <tr data-row>
                                <td style="width: 88px;">
                                    @if ($product->resolvedImageUrl())
                                        <img src="{{ $product->resolvedImageUrl() }}" alt="{{ $product->name }}" class="rounded-3 object-fit-cover" style="width:72px;height:72px;">
                                    @else
                                        <div class="rounded-3 bg-body-secondary d-flex align-items-center justify-content-center text-muted" style="width:72px;height:72px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $product->name }}</td>
                                <td><code class="small">{{ $product->slug }}</code></td>
                                <td>
                                    <div>{{ $product->category }}</div>
                                    @if (! $product->is_active)
                                        <span class="badge rounded-pill text-bg-secondary-subtle text-secondary border mt-1">Nonaktif</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#productModal" data-mode="edit"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ e($product->name) }}"
                                        data-category="{{ e($product->category) }}"
                                        data-theme="{{ e($product->theme) }}"
                                        data-price="{{ $product->price }}"
                                        data-image_url="{{ e($product->image_url) }}"
                                        data-description="{{ e($product->description ?? '') }}">Edit</button>
                                    @if (($product->orders_count ?? 0) === 0)
                                        <form method="post" action="{{ route('admin.products.destroy', $product) }}" class="d-inline" data-confirm="Hapus produk ini?" data-confirm-title="Hapus Produk" data-confirm-confirm-text="Ya, hapus">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Hapus</button>
                                        </form>
                                    @else
                                        <form method="post" action="{{ route('admin.products.destroy', $product) }}" class="d-inline" data-confirm="Produk dipakai di pesanan, nonaktifkan dari katalog?" data-confirm-title="Nonaktifkan Produk" data-confirm-confirm-text="Ya, nonaktifkan">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill" @disabled(! $product->is_active)>
                                                {{ $product->is_active ? 'Nonaktifkan' : 'Sudah nonaktif' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada produk. Jalankan seeder.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 glass">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="productModalTitle">Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="productForm" method="post" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="productFormMethod" value="" disabled>
                    <div class="modal-body pt-0">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input class="form-control" name="name" id="pf_name" required>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori</label>
                                <input class="form-control" name="category" id="pf_category">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tema</label>
                                <input class="form-control" name="theme" id="pf_theme">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" class="form-control" name="price" id="pf_price" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload gambar produk</label>
                            <input type="file" class="form-control" name="image_file" id="pf_image_file" accept="image/*">
                            <div class="form-text">Jika diisi, gambar upload dipakai di katalog.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">URL gambar lama / cadangan</label>
                            <input class="form-control" name="image_url" id="pf_image_url" placeholder="https://… atau path storage">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" id="pf_description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-gradient rounded-pill">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const modal = document.getElementById("productModal");
            const form = document.getElementById("productForm");
            const methodField = document.getElementById("productFormMethod");
            const storeUrl = @json(route('admin.products.store'));
            modal.addEventListener("show.bs.modal", function (e) {
                const btn = e.relatedTarget;
                const mode = btn?.getAttribute("data-mode") || "create";
                document.getElementById("productModalTitle").textContent = mode === "create" ? "Tambah produk" : "Edit produk";
                form.reset();
                methodField.value = "";
                methodField.disabled = mode !== "edit";
                form.action = storeUrl;
                if (mode === "edit") {
                    const id = btn.getAttribute("data-id");
                    form.action = @json(url('/admin/products')).replace(/\/$/, "") + "/" + id;
                    methodField.value = "PUT";
                    document.getElementById("pf_name").value = btn.getAttribute("data-name") || "";
                    document.getElementById("pf_category").value = btn.getAttribute("data-category") || "";
                    document.getElementById("pf_theme").value = btn.getAttribute("data-theme") || "";
                    document.getElementById("pf_price").value = btn.getAttribute("data-price") || "";
                    document.getElementById("pf_image_url").value = btn.getAttribute("data-image_url") || "";
                    document.getElementById("pf_image_file").value = "";
                    document.getElementById("pf_description").value = btn.getAttribute("data-description") || "";
                }
            });
        })();
    </script>
@endpush
