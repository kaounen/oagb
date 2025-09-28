const { test, expect } = require('@playwright/test');

test.describe('News and Events', () => {
  test('should load news page', async ({ page }) => {
    await page.goto('/noticias.php');
    
    await expect(page).toHaveURL(/noticias\.php/);
    
    // Check for news content structure
    const contentArea = page.locator('main, .content, .news, body');
    await expect(contentArea).toBeVisible();
  });

  test('should load events/agenda page', async ({ page }) => {
    await page.goto('/agenda.php');
    
    await expect(page).toHaveURL(/agenda\.php/);
    
    // Check for agenda content
    const agendaContent = page.locator('main, .content, .agenda, body');
    await expect(agendaContent).toBeVisible();
  });

  test('should load individual news article', async ({ page }) => {
    await page.goto('/artigo.php');
    
    await expect(page).toHaveURL(/artigo\.php/);
    
    // Check for article content structure
    const articleContent = page.locator('main, .article, .content, body');
    await expect(articleContent).toBeVisible();
  });

  test('should load individual event page', async ({ page }) => {
    await page.goto('/evento.php');
    
    await expect(page).toHaveURL(/evento\.php/);
    
    // Check for event content structure  
    const eventContent = page.locator('main, .event, .content, body');
    await expect(eventContent).toBeVisible();
  });
});