<?php
/**
 * app/Views/catalog/index.php
 * Dynamic Medication Catalog View.
 * Phase 06: Project Structure Refactoring
 */

use App\Core\View;
?>
<div style="margin-bottom: 2rem;">
  <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem;">Clinical Medication Catalog</h1>
  <p style="color: var(--color-text-muted, #64748b); margin: 0;">Explore certified pharmaceuticals, OTC remedies, pediatric care, and surgical supplies.</p>
</div>

<!-- Search & Filter Bar -->
<div style="background: var(--color-bg-surface, #ffffff); border: 1px solid var(--color-border-subtle, #e2e8f0); border-radius: 8px; padding: 1rem; margin-bottom: 2rem; display: flex; gap: 1rem;">
  <form method="GET" action="/catalog" style="display: flex; gap: 0.75rem; width: 100%;">
    <input type="text" name="q" value="<?= View::e($search ?? '') ?>" placeholder="Search medicine by brand name, generic formulation, or indication..." style="flex-grow: 1; padding: 0.65rem 1rem; border: 1px solid var(--color-border-default, #cbd5e1); border-radius: 6px; font-size: 0.95rem;">
    <button type="submit" style="background: var(--color-primary, #0e7490); color: #ffffff; border: none; padding: 0.65rem 1.5rem; border-radius: 6px; font-weight: 600; cursor: pointer;">Search</button>
  </form>
</div>

<!-- Products Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
  <?php if (empty($products)): ?>
    <div style="grid-column: 1 / -1; padding: 3rem; text-align: center; background: white; border-radius: 8px; border: 1px dashed #cbd5e1;">
      <p style="color: var(--color-text-muted, #64748b); font-size: 1.1rem; margin: 0;">No medications found matching your criteria.</p>
      <a href="/catalog" style="display: inline-block; margin-top: 1rem; color: var(--color-primary, #0e7490); font-weight: 600;">Clear Filters</a>
    </div>
  <?php else: ?>
    <?php foreach ($products as $product): ?>
      <div style="background: var(--color-bg-surface, #ffffff); border: 1px solid var(--color-border-subtle, #e2e8f0); border-radius: 10px; overflow: hidden; display: flex; flex-direction: column;">
        <div style="background: var(--color-bg-subtle, #f8fafc); height: 180px; display: flex; align-items: center; justify-content: center; padding: 1rem;">
          <img src="/assets/images/<?= View::e($product->image) ?>" alt="<?= View::e($product->name) ?>" style="max-height: 140px; max-width: 100%; object-fit: contain;">
        </div>
        <div style="padding: 1.25rem; display: flex; flex-direction: column; flex-grow: 1;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--color-primary, #0e7490);"><?= View::e($product->categoryName ?? 'Clinical') ?></span>
            <?php if ($product->requiresPrescription): ?>
              <span style="font-size: 0.7rem; font-weight: 700; background: #fee2e2; color: #dc2626; padding: 0.15rem 0.5rem; border-radius: 4px;">Rx Required</span>
            <?php endif; ?>
          </div>
          <h3 style="font-size: 1.1rem; font-weight: 600; margin: 0 0 0.5rem 0;"><?= View::e($product->name) ?></h3>
          <?php if ($product->genericName): ?>
            <p style="font-size: 0.85rem; color: var(--color-text-muted, #64748b); margin: 0 0 0.75rem 0; font-style: italic;"><?= View::e($product->genericName) ?></p>
          <?php endif; ?>
          <div style="margin-top: auto; display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--color-border-subtle, #f1f5f9);">
            <div>
              <span style="font-size: 1.25rem; font-weight: 700; color: var(--color-primary-800, #155e75);">$<?= number_format($product->price, 2) ?></span>
              <span style="font-size: 0.75rem; color: var(--color-text-muted, #64748b);">/ <?= View::e($product->packSize) ?></span>
            </div>
            <a href="/catalog/<?= View::e($product->slug) ?>" style="background: var(--color-primary, #0e7490); color: #ffffff; padding: 0.45rem 0.9rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-decoration: none;">Details</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
