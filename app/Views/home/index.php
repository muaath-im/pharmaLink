<?php
/**
 * app/Views/home/index.php
 * Dynamic Modern Home Page View with Live Database Clinical Catalog.
 * Phase 06: Project Structure Refactoring
 */

use App\Core\View;
?>
<!-- Hero Banner -->
<section style="background: linear-gradient(135deg, var(--color-primary-800, #155e75), var(--color-primary-900, #164e63)); color: #ffffff; border-radius: 12px; padding: 3rem; margin-bottom: 2.5rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 2rem;">
  <div style="max-width: 600px;">
    <span style="background: var(--color-primary-700, #0e7490); color: #ffffff; padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Clinical Quality Assured</span>
    <h1 style="color: #ffffff; font-size: 2.5rem; font-weight: 800; margin: 1rem 0; line-height: 1.2;">Your Trusted Prescription & Wellness Dispensary</h1>
    <p style="color: var(--color-primary-100, #cffafe); font-size: 1.1rem; line-height: 1.6; margin-bottom: 1.5rem;">Access authentic pharmaceuticals, verified lot batches, and licensed pharmacist triage with rapid, temperature-controlled delivery.</p>
    <div style="display: flex; gap: 1rem;">
      <a href="/catalog" style="background: var(--color-secondary-500, #14b8a6); color: #ffffff; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none;">Browse Catalog</a>
      <a href="/pages/contact.html" style="background: transparent; color: #ffffff; border: 1px solid rgba(255,255,255,0.4); padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none;">Consult Pharmacist</a>
    </div>
  </div>
  <div style="text-align: center;">
    <img src="/assets/images/logo.png" alt="PharmaLink Emblem" style="max-height: 160px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.3));">
  </div>
</section>

<!-- Featured Live Database Products -->
<section>
  <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem;">
    <div>
      <h2 style="font-size: 1.75rem; font-weight: 700; margin: 0 0 0.25rem 0;">Featured Clinical Medications</h2>
      <p style="color: var(--color-text-muted, #64748b); margin: 0;">Verified stock and real-time inventory lot tracking.</p>
    </div>
    <a href="/catalog" style="color: var(--color-primary, #0e7490); font-weight: 600; text-decoration: none;">View All Products &rarr;</a>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
    <?php foreach ($featuredProducts as $product): ?>
      <div style="background: var(--color-bg-surface, #ffffff); border: 1px solid var(--color-border-subtle, #e2e8f0); border-radius: 10px; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s ease, box-shadow 0.2s ease;">
        <div style="background: var(--color-bg-subtle, #f8fafc); height: 180px; display: flex; align-items: center; justify-content: center; padding: 1rem;">
          <img src="/assets/images/<?= View::e($product->image) ?>" alt="<?= View::e($product->name) ?>" style="max-height: 140px; max-width: 100%; object-fit: contain;">
        </div>
        <div style="padding: 1.25rem; display: flex; flex-direction: column; flex-grow: 1;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--color-primary, #0e7490);"><?= View::e($product->categoryName ?? 'Medicine') ?></span>
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
            <a href="/catalog/<?= View::e($product->slug) ?>" style="background: var(--color-primary, #0e7490); color: #ffffff; padding: 0.45rem 0.9rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-decoration: none;">View Details</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
