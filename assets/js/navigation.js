/**
 * Navigation - Mobile menu toggle and header scroll behavior
 */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.querySelector(".menu-toggle");
    const navigation = document.querySelector(".main-navigation");
    const header = document.getElementById("site-header");
    
    let lastScrollTop = 0;
    let scrollTimeout;
    const scrollThreshold = 100; // Minimum scroll before hiding header

    // Header scroll behavior
    if (header) {
      window.addEventListener("scroll", function () {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // Clear previous timeout
        clearTimeout(scrollTimeout);

        // Only apply behavior after scrolling past threshold
        if (scrollTop > scrollThreshold) {
          if (scrollTop > lastScrollTop) {
            // Scrolling down - hide header
            header.classList.add("header-hidden");
          } else {
            // Scrolling up - show header
            header.classList.remove("header-hidden");
          }
        } else {
          // At top of page - always show header
          header.classList.remove("header-hidden");
        }

        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
      });

      // Show header on hover when hidden
      header.addEventListener("mouseenter", function () {
        if (header.classList.contains("header-hidden")) {
          header.classList.add("header-peek");
        }
      });

      header.addEventListener("mouseleave", function () {
        header.classList.remove("header-peek");
      });

      // Detect mouse near top of screen to show header
      document.addEventListener("mousemove", function (e) {
        if (e.clientY < 50 && header.classList.contains("header-hidden")) {
          header.classList.add("header-peek");
        } else if (e.clientY > 150 && header.classList.contains("header-peek")) {
          header.classList.remove("header-peek");
        }
      });
    }

    // Mobile menu toggle
    if (!menuToggle) {
      return;
    }

    menuToggle.addEventListener("click", function () {
      const expanded =
        menuToggle.getAttribute("aria-expanded") === "true" || false;
      menuToggle.setAttribute("aria-expanded", !expanded);
      navigation.classList.toggle("toggled");
    });

    // Close menu when clicking outside
    document.addEventListener("click", function (event) {
      const isClickInside =
        navigation.contains(event.target) || menuToggle.contains(event.target);

      if (
        !isClickInside &&
        menuToggle.getAttribute("aria-expanded") === "true"
      ) {
        menuToggle.setAttribute("aria-expanded", "false");
        navigation.classList.remove("toggled");
      }
    });

    // Handle dropdown menus on mobile
    const menuItemsWithChildren = document.querySelectorAll(
      ".nav-menu .menu-item-has-children"
    );

    menuItemsWithChildren.forEach(function (item) {
      const link = item.querySelector("a");
      const submenu = item.querySelector(".sub-menu");

      if (window.innerWidth <= 768) {
        // Add click handler for mobile
        link.addEventListener("click", function (e) {
          if (submenu) {
            e.preventDefault();
            item.classList.toggle("open");
            submenu.style.display =
              submenu.style.display === "block" ? "none" : "block";
          }
        });
      }
    });
  });
})();
