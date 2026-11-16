<?php
/**
 * app/Views/partials/navbar.php
 * Top Navigation Bar with Quick Role Switcher.
 * Phase 06: Project Structure Refactoring
 */

use App\Core\View;
use App\Core\Session;

$isLoggedIn = Session::has('user_id');
$username   = Session::get('username', 'Guest');
$isAdmin    = Session::get('is_admin', 0) === 1;
?>
<header class="navbar-wrapper" style="background: var(--color-bg-surface, #ffffff); border-bottom: 1px solid var(--color-border-subtle, #e2e8f0); padding: 0.75rem 2rem;">
  <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 1.5rem;">
    <!-- Brand Logo -->
    <a href="/pages/home.html" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: var(--color-text-main, #0f172a);">
      <img src="/assets/images/logo.png" alt="PharmaLink Logo" style="height: 38px; width: auto;">
      <span style="font-weight: 700; font-size: 1.25rem; color: var(--color-primary, #0e7490);">Pharma<span style="color: var(--color-secondary, #0f766e);">Link</span></span>
    </a>

    <!-- Primary Navigation Links -->
    <nav style="display: flex; align-items: center; gap: 1.5rem; font-size: 0.95rem; font-weight: 500;">
      <a href="/pages/home.html" style="text-decoration: none; color: var(--color-text-secondary, #334155);">Home</a>
      <a href="/pages/shop/comm.html" style="text-decoration: none; color: var(--color-text-secondary, #334155);">Catalog</a>
      <a href="/pages/contact.html" style="text-decoration: none; color: var(--color-text-secondary, #334155);">Support</a>
      <?php if ($isAdmin): ?>
        <a href="/admin/index.php" style="text-decoration: none; color: var(--color-primary, #0e7490); font-weight: 600;">Admin Dashboard</a>
      <?php endif; ?>
    </nav>

    <!-- User / Auth / Recruiter Fast Switcher -->
    <div style="display: flex; align-items: center; gap: 1rem;">
      <?php if ($isLoggedIn): ?>
        <span style="font-size: 0.875rem; color: var(--color-text-muted, #64748b);">Logged in as <strong><?= View::e($username) ?></strong></span>
        <a href="/src/auth/logout.php" style="font-size: 0.85rem; color: var(--color-danger, #dc2626); text-decoration: none;">Sign Out</a>
      <?php else: ?>
        <a href="/pages/auth.html" style="padding: 0.45rem 1rem; border-radius: 6px; background: var(--color-primary, #0e7490); color: #ffffff; text-decoration: none; font-size: 0.875rem; font-weight: 500;">Sign In</a>
      <?php endif; ?>
    </div>
  </div>
</header>
