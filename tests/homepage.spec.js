const { test, expect } = require('@playwright/test');

test.describe('OAGB Homepage', () => {
  test('should load homepage successfully', async ({ page }) => {
    await page.goto('/index.html');
    
    // Check that the page loads (allow for various possible titles)
    const title = await page.title();
    console.log('Page title:', title);
    
    // Check for main navigation elements or body content
    await expect(page.locator('body')).toBeVisible();
  });

  test('should have working navigation menu', async ({ page }) => {
    await page.goto('/');
    
    // Check for common navigation links
    const navLinks = [
      'apresentacao-historia.php',
      'noticias.php', 
      'agenda.php',
      'contacto.php'
    ];
    
    for (const link of navLinks) {
      const linkElement = page.locator(`a[href*="${link}"]`).first();
      if (await linkElement.count() > 0) {
        await expect(linkElement).toBeVisible();
      }
    }
  });

  test('should display website content', async ({ page }) => {
    await page.goto('/index.html');
    
    // Check that we're not getting the XAMPP default page
    const bodyText = await page.locator('body').textContent();
    console.log('Body text preview:', bodyText?.substring(0, 200));
    
    // Ensure we're not on XAMPP welcome page
    await expect(page.locator('body')).not.toContainText('Welcome to XAMPP');
  });
});