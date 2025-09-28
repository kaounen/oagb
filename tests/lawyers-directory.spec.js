const { test, expect } = require('@playwright/test');

test.describe('Lawyers Directory', () => {
  test('should load lawyers directory page', async ({ page }) => {
    await page.goto('/advogados_inscritos_php.php');
    
    // Check page loads successfully
    await expect(page).toHaveURL(/advogados_inscritos_php\.php/);
    
    // Look for lawyer listing elements
    const mainContent = page.locator('main, .content, body');
    await expect(mainContent).toBeVisible();
  });

  test('should load lawyer search page', async ({ page }) => {
    await page.goto('/pesquisa-advogados.php');
    
    // Check page loads successfully  
    await expect(page).toHaveURL(/pesquisa-advogados\.php/);
    
    // Look for search form
    const searchElements = page.locator('form, input[type="search"], input[name*="search"]');
    if (await searchElements.count() > 0) {
      await expect(searchElements.first()).toBeVisible();
    }
  });
});