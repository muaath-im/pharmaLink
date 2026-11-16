<?php
/**
 * app/Views/partials/footer.php
 * Clinical Compliance and Regulatory Footer.
 * Phase 06: Project Structure Refactoring
 */
?>
<footer style="background: var(--color-bg-subtle, #f1f5f9); border-top: 1px solid var(--color-border-subtle, #e2e8f0); padding: 2.5rem 2rem; margin-top: 4rem; color: var(--color-text-muted, #475569); font-size: 0.875rem;">
  <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 2rem;">
    <div>
      <div style="font-weight: 700; color: var(--color-primary, #0e7490); font-size: 1.1rem; margin-bottom: 0.5rem;">PharmaLink Online Pharmacy Portal</div>
      <p style="margin: 0; max-width: 400px; line-height: 1.5;">Licensed clinical dispensary operations. Compliant with HIPAA, NABP, and digital pharmaceutical dispensing standards.</p>
    </div>
    <div>
      <div style="font-weight: 600; color: var(--color-text-main, #0f172a); margin-bottom: 0.5rem;">Clinical Standards</div>
      <p style="margin: 0;">Prescription Verification Protocol (21 CFR Part 11)</p>
      <p style="margin: 0.25rem 0 0 0;">FEFO Inventory Expiry Assurance Protocol</p>
    </div>
    <div>
      <div style="font-weight: 600; color: var(--color-text-main, #0f172a); margin-bottom: 0.5rem;">Engineering Portfolio</div>
      <p style="margin: 0;">Modernized PHP 8.2+ MVC & MySQL 8.0 Architecture</p>
      <p style="margin: 0.25rem 0 0 0;">&copy; <?= date('Y') ?> PharmaLink. All Rights Reserved.</p>
    </div>
  </div>
</footer>
