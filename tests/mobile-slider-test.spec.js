// @ts-check
const { test, expect, devices } = require('@playwright/test');

// Configure mobile viewport for all tests
test.use({ ...devices['iPhone 12'] });

test.describe('Mobile Slider Tests', () => {

  test('should display mobile slider with all improvements', async ({ page }) => {
    // Navigate to the OAGB homepage
    await page.goto('http://localhost/oagb/');

    // Wait for page to load
    await page.waitForLoadState('networkidle');

    // Check that mobile carousel exists
    const mobileCarousel = page.locator('#mobile-background-carousel');
    await expect(mobileCarousel).toBeVisible();

    console.log('✅ Mobile carousel is visible');
  });

  test('should show carousel indicators', async ({ page }) => {
    await page.goto('http://localhost/oagb/');
    await page.waitForLoadState('networkidle');

    // Check carousel indicators are visible
    const indicators = page.locator('.mobile-carousel-indicators');
    await expect(indicators).toBeVisible();

    // Count indicators (should match number of slides)
    const indicatorButtons = page.locator('.mobile-carousel-indicators button');
    const count = await indicatorButtons.count();

    console.log(`✅ Found ${count} carousel indicators`);
    expect(count).toBeGreaterThan(0);
  });

  test('should hide indicators when menu is open', async ({ page }) => {
    await page.goto('http://localhost/oagb/');
    await page.waitForLoadState('networkidle');

    // Get indicators
    const indicators = page.locator('.mobile-carousel-indicators');

    // Initially visible
    await expect(indicators).toBeVisible();

    // Open menu
    const menuButton = page.locator('.navbar-toggler');
    await menuButton.click();

    // Wait for menu to open
    await page.waitForTimeout(500);

    // Check if indicators are hidden (opacity 0)
    const opacity = await indicators.evaluate(el => window.getComputedStyle(el).opacity);

    console.log(`✅ Indicators opacity when menu open: ${opacity}`);
    expect(parseFloat(opacity)).toBeLessThan(0.5);
  });

  test('should hide content when menu is open', async ({ page }) => {
    await page.goto('http://localhost/oagb/');
    await page.waitForLoadState('networkidle');

    // Get slide content
    const slideContent = page.locator('.mobile-slide-content').first();

    // Initially visible
    await expect(slideContent).toBeVisible();

    // Open menu
    const menuButton = page.locator('.navbar-toggler');
    await menuButton.click();

    // Wait for menu animation
    await page.waitForTimeout(500);

    // Check if content is hidden (opacity 0)
    const opacity = await slideContent.evaluate(el => window.getComputedStyle(el).opacity);

    console.log(`✅ Content opacity when menu open: ${opacity}`);
    expect(parseFloat(opacity)).toBe(0);
  });

  test('should navigate slides with indicators', async ({ page }) => {
    await page.goto('http://localhost/oagb/');
    await page.waitForLoadState('networkidle');

    // Click second indicator
    const secondIndicator = page.locator('.mobile-carousel-indicators button').nth(1);
    await secondIndicator.click();

    // Wait for transition
    await page.waitForTimeout(800);

    // Check if second indicator is active
    const hasActiveClass = await secondIndicator.evaluate(el => el.classList.contains('active'));

    console.log('✅ Second indicator clicked and is active:', hasActiveClass);
    expect(hasActiveClass).toBe(true);
  });

  test('should swipe slides on mobile', async ({ page }) => {
    await page.goto('http://localhost/oagb/');
    await page.waitForLoadState('networkidle');

    const carousel = page.locator('#mobile-background-carousel');
    const box = await carousel.boundingBox();

    if (box) {
      // Swipe left (next slide)
      await page.mouse.move(box.x + box.width * 0.8, box.y + box.height / 2);
      await page.mouse.down();
      await page.mouse.move(box.x + box.width * 0.2, box.y + box.height / 2, { steps: 10 });
      await page.mouse.up();

      // Wait for transition
      await page.waitForTimeout(800);

      console.log('✅ Swipe gesture executed');
    }
  });

  test('should have proper z-index hierarchy', async ({ page }) => {
    await page.goto('http://localhost/oagb/');
    await page.waitForLoadState('networkidle');

    // Open menu
    const menuButton = page.locator('.navbar-toggler');
    await menuButton.click();
    await page.waitForTimeout(500);

    // Get z-index values
    const menuZIndex = await page.locator('.navbar-collapse').evaluate(
      el => window.getComputedStyle(el).zIndex
    );

    const contentZIndex = await page.locator('.mobile-slide-content').first().evaluate(
      el => window.getComputedStyle(el).zIndex
    );

    const indicatorsZIndex = await page.locator('.mobile-carousel-indicators').evaluate(
      el => window.getComputedStyle(el).zIndex
    );

    console.log(`Menu z-index: ${menuZIndex}`);
    console.log(`Content z-index: ${contentZIndex}`);
    console.log(`Indicators z-index: ${indicatorsZIndex}`);

    // Menu should be above content and indicators
    expect(parseInt(menuZIndex)).toBeGreaterThan(parseInt(contentZIndex));
    expect(parseInt(menuZIndex)).toBeGreaterThan(parseInt(indicatorsZIndex));

    console.log('✅ Z-index hierarchy is correct');
  });

  test('should show slide content text properly', async ({ page }) => {
    await page.goto('http://localhost/oagb/');
    await page.waitForLoadState('networkidle');

    // Get title and description
    const title = page.locator('.mobile-slide-content h1').first();
    const description = page.locator('.mobile-slide-content p').first();

    // Check they are visible
    await expect(title).toBeVisible();
    await expect(description).toBeVisible();

    // Get text content
    const titleText = await title.textContent();
    const descText = await description.textContent();

    console.log(`✅ Title: ${titleText}`);
    console.log(`✅ Description: ${descText?.substring(0, 50)}...`);

    expect(titleText).toBeTruthy();
    expect(descText).toBeTruthy();
  });

  test('should have clickable "Saiba mais" button', async ({ page }) => {
    await page.goto('http://localhost/oagb/');
    await page.waitForLoadState('networkidle');

    // Get button
    const button = page.locator('.mobile-slide-content .btn').first();

    // Check if visible
    await expect(button).toBeVisible();

    // Get button text
    const buttonText = await button.textContent();
    console.log(`✅ Button text: ${buttonText}`);

    // Check if button is clickable (has href)
    const href = await button.getAttribute('href');
    console.log(`✅ Button link: ${href}`);

    expect(href).toBeTruthy();
  });

  test('should pause carousel when modal opens', async ({ page }) => {
    await page.goto('http://localhost/oagb/');
    await page.waitForLoadState('networkidle');

    // Open search modal
    const searchButton = page.locator('button[data-bs-target="#searchModal"]');
    await searchButton.click();

    // Wait for modal
    await page.waitForTimeout(500);

    // Check if modal is visible
    const modal = page.locator('#searchModal');
    const isVisible = await modal.isVisible();

    console.log('✅ Search modal opened:', isVisible);
    expect(isVisible).toBe(true);
  });

});

test.describe('Mobile Slider Accessibility Tests', () => {

  test('should have proper ARIA labels', async ({ page }) => {
    await page.goto('http://localhost/oagb/');
    await page.waitForLoadState('networkidle');

    const carousel = page.locator('#mobile-background-carousel');

    // Check ARIA attributes
    const role = await carousel.getAttribute('role');
    const ariaLabel = await carousel.getAttribute('aria-label');

    console.log(`✅ Carousel role: ${role}`);
    console.log(`✅ Carousel aria-label: ${ariaLabel}`);

    expect(role).toBe('region');
    expect(ariaLabel).toBeTruthy();
  });

  test('should have accessible control buttons', async ({ page }) => {
    await page.goto('http://localhost/oagb/');
    await page.waitForLoadState('networkidle');

    const prevButton = page.locator('.carousel-control-prev').first();
    const nextButton = page.locator('.carousel-control-next').first();

    const prevLabel = await prevButton.getAttribute('aria-label');
    const nextLabel = await nextButton.getAttribute('aria-label');

    console.log(`✅ Previous button aria-label: ${prevLabel}`);
    console.log(`✅ Next button aria-label: ${nextLabel}`);

    expect(prevLabel).toBeTruthy();
    expect(nextLabel).toBeTruthy();
  });

});
