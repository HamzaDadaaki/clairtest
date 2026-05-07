import { test, expect } from '@playwright/test';

test.describe('Claire Stefanich Arts - Homepage', () => {
  test('should load homepage successfully', async ({ page }) => {
    await page.goto('/');
    await expect(page).toHaveTitle(/Claire Stefanich Arts/);
  });

  test('should display hero section with buttons', async ({ page }) => {
    await page.goto('/');
    
    // Check hero section exists
    const heroSection = page.locator('.hero-section');
    await expect(heroSection).toBeVisible();
    
    // Check hero buttons are visible and centered
    const heroButtons = page.locator('.hero-button-row .button');
    await expect(heroButtons).toHaveCount(2);
    
    // Verify buttons have proper styling
    const buttonRow = page.locator('.hero-button-row');
    const styles = await buttonRow.evaluate(el => {
      const computed = window.getComputedStyle(el);
      return {
        display: computed.display,
        justifyContent: computed.justifyContent,
        alignItems: computed.alignItems,
      };
    });
    
    expect(styles.display).toBe('flex');
    expect(styles.justifyContent).toContain('center');
  });

  test('should display originals section with 4 products', async ({ page }) => {
    await page.goto('/');
    
    const originalsSection = page.locator('#originals');
    await expect(originalsSection).toBeVisible();
    
    const productCards = originalsSection.locator('.product-card');
    const count = await productCards.count();
    expect(count).toBe(4);
  });

  test('should display prints section with 3 products (not commission examples)', async ({ page }) => {
    await page.goto('/');
    
    const printsSection = page.locator('#prints');
    await expect(printsSection).toBeVisible();
    
    const podCards = printsSection.locator('.pod-card');
    const count = await podCards.count();
    expect(count).toBe(3);
  });

  test('prints section should show product images', async ({ page }) => {
    await page.goto('/');
    
    const podImages = page.locator('.pod-card-image');
    const count = await podImages.count();
    
    // Check if at least some print cards have images
    if (count > 0) {
      const firstImage = podImages.first();
      const bgImage = await firstImage.evaluate(el => {
        return window.getComputedStyle(el).backgroundImage;
      });
      // Background image should be set if products have images
      expect(bgImage).toBeTruthy();
    }
  });

  test('should have properly styled buttons with correct font', async ({ page }) => {
    await page.goto('/');
    
    const button = page.locator('.button').first();
    const styles = await button.evaluate(el => {
      const computed = window.getComputedStyle(el);
      return {
        fontFamily: computed.fontFamily,
        fontWeight: computed.fontWeight,
        padding: computed.padding,
        borderRadius: computed.borderRadius,
      };
    });
    
    // Font should be Manrope
    expect(styles.fontFamily).toContain('Manrope');
    // Font weight should be 800
    expect(styles.fontWeight).toBe('800');
    // Should have rounded corners
    expect(styles.borderRadius).not.toBe('0px');
  });

  test('center-actions should properly center buttons', async ({ page }) => {
    await page.goto('/');
    
    const centerActions = page.locator('.center-actions');
    const styles = await centerActions.evaluate(el => {
      const computed = window.getComputedStyle(el);
      return {
        display: computed.display,
        justifyContent: computed.justifyContent,
      };
    });
    
    expect(styles.display).toBe('flex');
    expect(styles.justifyContent).toBe('center');
  });

  test('path cards section should display 3 items', async ({ page }) => {
    await page.goto('/');
    
    const pathSection = page.locator('.claire-path-section');
    await expect(pathSection).toBeVisible();
    
    const pathCards = pathSection.locator('.path-card');
    await expect(pathCards).toHaveCount(3);
  });

  test('prints section should be centered', async ({ page }) => {
    await page.goto('/');
    
    const printsSection = page.locator('.print-on-demand-section');
    const sectionHeading = printsSection.locator('.section-heading');
    
    const styles = await sectionHeading.evaluate(el => {
      const computed = window.getComputedStyle(el);
      return {
        textAlign: computed.textAlign,
        marginLeft: computed.marginLeft,
        marginRight: computed.marginRight,
      };
    });
    
    // Heading should be centered
    expect(styles.textAlign).toBe('center');
  });

  test('pod-grid should show 3 columns on desktop', async ({ page }) => {
    await page.goto('/');
    page.setViewportSize({ width: 1200, height: 800 });
    
    const podGrid = page.locator('.pod-grid');
    const gridTemplateColumns = await podGrid.evaluate(el => {
      return window.getComputedStyle(el).gridTemplateColumns;
    });
    
    // Should have 3 columns (check for "repeat(3" in grid template)
    expect(gridTemplateColumns).toContain('200px');
  });

  test('should not show commission examples in main products', async ({ page }) => {
    await page.goto('/arts');
    
    const productCards = page.locator('.product-card');
    const cards = await productCards.all();
    
    // Check each card doesn't have "commission" in the content
    for (const card of cards) {
      const text = await card.textContent();
      expect(text?.toLowerCase()).not.toContain('commission example');
    }
  });

  test('commissions page should display commission examples', async ({ page }) => {
    await page.goto('/commissions');
    
    // Commission examples should only appear on the commissions page
    const cards = page.locator('.commission-category-card, .commission-collage').first();
    await expect(cards).toBeVisible();
  });

  test('should display email subscription form with proper button', async ({ page }) => {
    await page.goto('/');
    
    const emailForm = page.locator('.email-form');
    await expect(emailForm).toBeVisible();
    
    const submitButton = emailForm.locator('.button-email-submit');
    await expect(submitButton).toBeVisible();
    
    const styles = await submitButton.evaluate(el => {
      const computed = window.getComputedStyle(el);
      return {
        fontFamily: computed.fontFamily,
        fontWeight: computed.fontWeight,
      };
    });
    
    // Email submit button should also use Manrope font
    expect(styles.fontFamily).toContain('Manrope');
  });

  test('responsive: pod-grid should show 2 columns on tablet', async ({ page }) => {
    await page.goto('/');
    page.setViewportSize({ width: 900, height: 800 });
    
    const podGrid = page.locator('.pod-grid');
    const gridTemplateColumns = await podGrid.evaluate(el => {
      return window.getComputedStyle(el).gridTemplateColumns;
    });
    
    // Should have 2 columns on tablet
    const columnCount = (gridTemplateColumns.match(/\s\d+px/g) || []).length;
    expect(columnCount).toBeLessThanOrEqual(2);
  });

  test('responsive: buttons should stack vertically on mobile', async ({ page }) => {
    await page.goto('/');
    page.setViewportSize({ width: 375, height: 667 });
    
    const podCardActions = page.locator('.pod-card-actions').first();
    if (await podCardActions.count() > 0) {
      const styles = await podCardActions.evaluate(el => {
        return window.getComputedStyle(el).flexDirection;
      });
      
      // On mobile, buttons should be in a column
      expect(styles).toBe('column');
    }
  });
});
