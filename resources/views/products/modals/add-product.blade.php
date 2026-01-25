
<!-- Modal إضافة منتج جديد -->
<div class="modal fade" id="addProductModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content modal-custom rounded-4 shadow-lg">
      <div class="modal-header modal-header-custom rounded-top-4">
        <button type="button" class="btn-close me-auto" data-bs-dismiss="modal"></button>
        <h5 class="modal-title mx-auto">منتج جديد</h5>
      </div>
      <form action="{{ route('products.store') }}" method="POST" class="modal-body p-4">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">معرف المنتج</label>
            <input type="text" name="product_id" class="form-control form-control-lg" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">اسم المنتج</label>
            <input type="text" name="name" class="form-control form-control-lg" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">السعر</label>
            <input type="number" step="0.01" name="price" class="form-control form-control-lg" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">التكلفة</label>
            <input type="number" step="0.01" name="cost" class="form-control form-control-lg" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">الكمية</label>
            <input type="number" name="quantity" class="form-control form-control-lg" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">الحد الأدنى للكمية</label>
            <input type="number" name="min_quantity" class="form-control form-control-lg"
              value="{{ old('min_quantity') }}" required>
          </div>
        </div>

        <div class="text-center mt-4">
          <button type="submit" class="btn btn-gradient btn-lg px-5 fw-bold">حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
/* مودال حسب الثيم */
.modal-custom {
    background: rgba(221, 205, 188, 0.15);
    backdrop-filter: blur(14px);
    border: 1px solid rgba(221, 205, 188, 0.3);
    color: var(--white);
    transform: translateX(-200%) rotate(-10deg);
    transition: transform 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55);
}

.modal.show .modal-custom {
    transform: translateX(0) rotate(0deg);
}

.modal-header-custom {
    background: linear-gradient(135deg, var(--prime), var(--prime-soft));
    color: var(--bg);
    font-weight: 700;
    font-size: 18px;
    border-bottom: none;
    display: flex;
    justify-content: center;
    position: relative;
}

/* زر الإغلاق */
.modal-header-custom .btn-close {
    filter: brightness(0.9);
}

/* أزرار المودال */
.btn-gradient {
    background: linear-gradient(135deg, var(--prime), var(--prime-soft));
    color: var(--bg);
    font-weight: 900;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    transition: transform 0.3s, box-shadow 0.3s;
}

.btn-gradient:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 12px 28px rgba(0,0,0,0.45);
}

.btn-gradient-alt {
    background: linear-gradient(135deg, var(--bg-dark), var(--bg));
    color: var(--prime);
    font-weight: 900;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    transition: transform 0.3s, box-shadow 0.3s;
}

.btn-gradient-alt:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 12px 28px rgba(0,0,0,0.45);
}
</style>
