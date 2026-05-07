import { test, expect } from '@playwright/test';

test.describe('Visual smoke', () => {
  test('Home hero & product cards render', async ({ page }) => {
    await page.goto('/');
    await expect(page.locator('.hero-shell')).toBeVisible();
    await expect(page.locator('.pod-grid .pod-card').first()).toBeVisible();
    await expect(page.locator('.hero-button-row .button').first()).toBeVisible();
  });

  test('Product gallery works', async ({ page }) => {
    await page.goto('/arts');
    const first = page.locator('.product-card').first();
    await expect(first).toBeVisible();
    await first.click();
    await expect(page.locator('.gallery-main')).toBeVisible();
    const nextButton = page.locator('.gallery-next');
    if (await nextButton.count()) {
      await nextButton.click();
    }
  });

  test('Cart and checkout summary render', async ({ page }) => {
    await page.goto('/arts');
    const first = page.locator('.product-card').first();
    await first.click();
    await page.locator('form[action*="/cart/add/"] button').first().click();
    await page.goto('/cart');
    await expect(page.locator('.cart-row').first()).toBeVisible();
    await page.goto('/checkout');
    await expect(page.locator('.order-summary-section')).toBeVisible();
    await expect(page.locator('.selected-products-box li').first()).toBeAttached();
  });
});
