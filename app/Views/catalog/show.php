<?php
/**
 * app/Views/catalog/show.php
 * Clinical Drug Detail View with FEFO Batch Traceability.
 * Phase 06: Project Structure Refactoring
 */

use App\Core\View;
?>
<div style="margin-bottom: 1.5rem;">
  <a href="/catalog" style="color: var(--color-primary, #0e7490); text-decoration: none; font-weight: 500;">&larr; Back to Medication Catalog</a>
</div>

<div style="background: var(--color-bg-surface, #ffffff); border: 1px solid var(--color-border-subtle, #e2e8f0); border-radius: 12px; padding: 2.5rem; display: flex; flex-wrap: wrap; gap: 3rem;">
  <!-- Product Image Well -->
  <div style="flex: 1 1 320px; background: var(--color-bg-subtle, #f8fafc); border-radius: 8px; display: flex; align-items: center; justify-content: center; min-height: 320px; padding: 2rem;">
    <img src="/assets/images/<?= View::e($product->image) ?>" alt="<?= View::e($product->name) ?>" style="max-height: 260px; max-width: 100%; object-fit: contain;">
  </div>

  <!-- Product Clinical Specifications -->
  <div style="flex: 2 1 420px; display: flex; flex-direction: column;">
    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
      <span style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; background: var(--color-primary-50, #ecfeff); color: var(--color-primary-700, #0e7490); padding: 0.2rem 0.6rem; border-radius: 4px;">
        <?= View::e($product->categoryName ?? 'Clinical Drug') ?>
      </span>
      <?php if ($product->requiresPrescription): ?>
        <span style="font-size: 0.75rem; font-weight: 700; background: #fee2e2; color: #dc2626; padding: 0.2rem 0.6rem; border-radius: 4px;">
          Prescription Required (Rx)
        </span>
      <?php else: ?>
        <span style="font-size: 0.75rem; font-weight: 600; background: #dcfce7; color: #166534; padding: 0.2rem 0.6rem; border-radius: 4px;">
          Over-The-Counter (OTC)
        </span>
      <?php endif; ?>
    </div>

    <h1 style="font-size: 2.2rem; font-weight: 800; margin: 0 0 0.5rem 0;"><?= View::e($product->name) ?></h1>
    <?php if ($product->genericName): ?>
      <p style="font-size: 1.1rem; color: var(--color-text-muted, #475569); margin: 0 0 1rem 0; font-style: italic;">
        Active Formulation: <?= View::e($product->genericName) ?>
      </p>
    <?php endif; ?>

    <div style="font-size: 1.85rem; font-weight: 800; color: var(--color-primary-800, #155e75); margin: 1rem 0;">
      $<?= number_format($product->price, 2) ?>
      <span style="font-size: 0.9rem; font-weight: 400; color: var(--color-text-muted, #64748b);">/ <?= View::e($product->packSize) ?></span>
    </div>

    <p style="font-size: 1rem; line-height: 1.7; color: var(--color-text-secondary, #334155); margin-bottom: 2rem;">
      <?= nl2br(View::e($product->description)) ?>
    </p>

    <!-- Clinical Details Grid -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; padding: 1.25rem; background: var(--color-bg-subtle, #f8fafc); border-radius: 8px; margin-bottom: 2rem;">
      <div>
        <span style="font-size: 0.8rem; color: var(--color-text-muted, #64748b); text-transform: uppercase;">SKU Identifier</span>
        <div style="font-weight: 600; font-family: monospace;"><?= View::e($product->sku) ?></div>
      </div>
      <div>
        <span style="font-size: 0.8rem; color: var(--color-text-muted, #64748b); text-transform: uppercase;">Dosage Form</span>
        <div style="font-weight: 600; text-transform: capitalize;"><?= View::e($product->dosageForm) ?> (<?= View::e($product->strength ?? 'Standard') ?>)</div>
      </div>
      <div>
        <span style="font-size: 0.8rem; color: var(--color-text-muted, #64748b); text-transform: uppercase;">Pack Size</span>
        <div style="font-weight: 600;"><?= View::e($product->packSize) ?></div>
      </div>
      <div>
        <span style="font-size: 0.8rem; color: var(--color-text-muted, #64748b); text-transform: uppercase;">Available Stock</span>
        <div style="font-weight: 600; color: <?= $product->stockQuantity > 0 ? '#16a34a' : '#dc2626' ?>;">
          <?= $product->stockQuantity ?> units available
        </div>
      </div>
    </div>

    <!-- Active FEFO Lot Batches Section -->
    <div style="border-top: 1px solid var(--color-border-subtle, #e2e8f0); padding-top: 1.5rem;">
      <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.75rem;">Verified Lot Batches (FEFO Tracking)</h3>
      <?php if (empty($batches)): ?>
        <p style="color: var(--color-text-muted, #64748b); font-size: 0.9rem;">No active inventory lots allocated for this product.</p>
      <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
          <thead>
            <tr style="text-align: left; border-bottom: 2px solid var(--color-border-default, #cbd5e1); color: var(--color-text-secondary, #334155);">
              <th style="padding: 0.5rem 0;">Batch Lot #</th>
              <th style="padding: 0.5rem 0;">Quantity</th>
              <th style="padding: 0.5rem 0;">Expiry Date</th>
              <th style="padding: 0.5rem 0;">Manufacturer / Supplier</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($batches as $batch): ?>
              <tr style="border-bottom: 1px solid var(--color-border-subtle, #e2e8f0);">
                <td style="padding: 0.65rem 0; font-family: monospace; font-weight: 600;"><?= View::e($batch->batchNumber) ?></td>
                <td style="padding: 0.65rem 0;"><?= $batch->getAvailableQuantity() ?> units</td>
                <td style="padding: 0.65rem 0; color: <?= $batch->isNearExpiry() ? '#d97706' : '#166534' ?>; font-weight: 600;">
                  <?= View::e($batch->expiryDate) ?> <?= $batch->isNearExpiry() ? '(! <90d)' : '' ?>
                </td>
                <td style="padding: 0.65rem 0; color: var(--color-text-muted, #64748b);"><?= View::e($batch->supplierName ?? 'Pharma Certified') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</div>
