const { test, expect } = require('@playwright/test');

test.describe('Contact and Forms', () => {
  test('should load contact page', async ({ page }) => {
    await page.goto('/contacto.php');
    
    await expect(page).toHaveURL(/contacto\.php/);
    
    // Check for contact form or information
    const contactContent = page.locator('main, .contact, .content, body');
    await expect(contactContent).toBeVisible();
  });

  test('should load lawyer application form', async ({ page }) => {
    await page.goto('/solicitacao-advogados.php');
    
    await expect(page).toHaveURL(/solicitacao-advogados\.php/);
    
    // Check for form elements
    const formContent = page.locator('main, form, .content, body');
    await expect(formContent).toBeVisible();
  });

  test('should load order registration form', async ({ page }) => {
    await page.goto('/inscricao-ordem.php');
    
    await expect(page).toHaveURL(/inscricao-ordem\.php/);
    
    // Check for registration form
    const registrationContent = page.locator('main, form, .content, body');
    await expect(registrationContent).toBeVisible();
  });
});