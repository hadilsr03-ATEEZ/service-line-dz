/* ============================================
   ServiceLine DZ - Main JavaScript
   ============================================ */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
  initNavbar();
  initMobileMenu();
  initScrollAnimations();
  initPasswordToggle();
  initSearchFilters();
  initTabs();
  initCounterAnimations();
});

/* ============================================
   Navigation
   ============================================ */
function initNavbar() {
  const navbar = document.querySelector('.navbar');
  if (!navbar) return;

  window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
}

/* ============================================
   Mobile Menu
   ============================================ */
function initMobileMenu() {
  const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
  const mobileNav = document.querySelector('.mobile-nav');
  
  if (!mobileMenuBtn || !mobileNav) return;

  mobileMenuBtn.addEventListener('click', () => {
    mobileNav.classList.toggle('active');
    mobileMenuBtn.classList.toggle('active');
  });

  // Close menu when clicking on a link
  const mobileLinks = mobileNav.querySelectorAll('a');
  mobileLinks.forEach(link => {
    link.addEventListener('click', () => {
      mobileNav.classList.remove('active');
      mobileMenuBtn.classList.remove('active');
    });
  });
}

/* ============================================
   Scroll Animations
   ============================================ */
function initScrollAnimations() {
  const animatedElements = document.querySelectorAll('.animate-on-scroll');
  
  if (animatedElements.length === 0) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  });

  animatedElements.forEach(el => observer.observe(el));
}

/* ============================================
   Password Toggle
   ============================================ */
function initPasswordToggle() {
  const toggleButtons = document.querySelectorAll('.toggle-password');
  
  toggleButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      const input = this.parentElement.querySelector('input');
      const icon = this.querySelector('svg');
      
      if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `
          <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
          <line x1="1" y1="1" x2="23" y2="23"></line>
        `;
      } else {
        input.type = 'password';
        icon.innerHTML = `
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
          <circle cx="12" cy="12" r="3"></circle>
        `;
      }
    });
  });
}

/* ============================================
   Search Filters
   ============================================ */
function initSearchFilters() {
  const clearFiltersBtn = document.querySelector('.filters-header button');
  
  if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener('click', function() {
      const checkboxes = document.querySelectorAll('.filter-option input[type="checkbox"]');
      const inputs = document.querySelectorAll('.price-range input');
      
      checkboxes.forEach(cb => cb.checked = false);
      inputs.forEach(input => input.value = '');
    });
  }

  // Filter functionality
  const filterCheckboxes = document.querySelectorAll('.filter-option input[type="checkbox"]');
  filterCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      applyFilters();
    });
  });
}

function applyFilters() {
  // This would filter the results in a real application
  console.log('Filters applied');
}

/* ============================================
   Tabs
   ============================================ */
function initTabs() {
  const tabButtons = document.querySelectorAll('[data-tab]');
  const tabContents = document.querySelectorAll('[data-tab-content]');
  
  if (tabButtons.length === 0) return;

  tabButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      const targetTab = this.getAttribute('data-tab');
      
      // Remove active class from all buttons and contents
      tabButtons.forEach(b => b.classList.remove('active'));
      tabContents.forEach(c => c.classList.remove('active'));
      
      // Add active class to clicked button and corresponding content
      this.classList.add('active');
      document.querySelector(`[data-tab-content="${targetTab}"]`)?.classList.add('active');
    });
  });
}

/* ============================================
   Counter Animations
   ============================================ */
function initCounterAnimations() {
  const counters = document.querySelectorAll('.stat-value, .hero-stat-value');
  
  if (counters.length === 0) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach(counter => observer.observe(counter));
}

function animateCounter(element) {
  const text = element.textContent;
  const match = text.match(/(\d+)/);
  
  if (!match) return;
  
  const target = parseInt(match[1]);
  const suffix = text.replace(match[1], '');
  const duration = 2000;
  const step = target / (duration / 16);
  let current = 0;
  
  const timer = setInterval(() => {
    current += step;
    if (current >= target) {
      element.textContent = target + suffix;
      clearInterval(timer);
    } else {
      element.textContent = Math.floor(current) + suffix;
    }
  }, 16);
}

/* ============================================
   Form Validation
   ============================================ */
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function validatePhone(phone) {
  const re = /^(\+213|0)(5|6|7)[0-9]{8}$/;
  return re.test(phone.replace(/\s/g, ''));
}

function validatePassword(password) {
  return password.length >= 8;
}

function showError(input, message) {
  const formGroup = input.closest('.form-group');
  const existingError = formGroup.querySelector('.form-error');
  
  if (existingError) {
    existingError.textContent = message;
  } else {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'form-error';
    errorDiv.textContent = message;
    formGroup.appendChild(errorDiv);
  }
  
  input.classList.add('error');
}

function clearError(input) {
  const formGroup = input.closest('.form-group');
  const existingError = formGroup.querySelector('.form-error');
  
  if (existingError) {
    existingError.remove();
  }
  
  input.classList.remove('error');
}

/* ============================================
   Login Form Handler
   ============================================ */
function handleLogin(event) {
  event.preventDefault();

  const form = event.target;

  const emailOrPhone =
    form.querySelector('[name="emailOrPhone"]').value.trim();

  const password =
    form.querySelector('[name="password"]').value;

  if (!emailOrPhone || !password) {
    alert("Please fill all fields");
    return;
  }

  const btn =
    form.querySelector('button[type="submit"]');

  btn.disabled = true;
  btn.textContent = "Signing in...";

  fetch("api/login.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      emailOrPhone,
      password
    })
  })
  .then(response => response.json())
  .then(data => {

    if (data.error) {

      alert(data.error);

      btn.disabled = false;
      btn.textContent = "Sign In";

      return;
    }

    alert("Login successful");

    localStorage.setItem(
      "userId",
      data.userId
    );

    localStorage.setItem(
      "userType",
      data.userType
    );

    if (data.userType === "provider") {

      window.location.href =
        "create_profile.html";

    } else {

      window.location.href =
        "search.html";

    }

  })
  .catch(error => {

    console.error(error);

    alert("Something went wrong");

    btn.disabled = false;
    btn.textContent = "Sign In";

  });
}

/* ============================================
   Registration Form Handler
   ============================================ */
function handleRegistration(event) {
  event.preventDefault();

  const form = event.target;

  const fullName = form.querySelector('[name="fullName"]').value.trim();
  const email = form.querySelector('[name="email"]').value.trim();
  const phone = form.querySelector('[name="phone"]').value.trim();
  const password = form.querySelector('[name="password"]').value;
  const confirmPassword = form.querySelector('[name="confirmPassword"]').value;
  const userType = form.querySelector('[name="userType"]:checked').value;

  if (!fullName || !email || !phone || !password || !confirmPassword) {
    alert("Please fill all fields");
    return;
  }

  if (password !== confirmPassword) {
    alert("Passwords do not match");
    return;
  }

  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = "Creating account...";

  fetch("api/register.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      fullName,
      email,
      phone,
      password,
      userType
    })
  })
  .then(response => response.json())
  .then(data => {

    if (data.error) {
      alert(data.error);
      btn.disabled = false;
      btn.textContent = "Create Account";
      return;
    }

    alert("Registration successful!");

    window.location.href = "login.html";

  })
  .catch(error => {
    console.error(error);

    alert("Something went wrong");

    btn.disabled = false;
    btn.textContent = "Create Account";
  });
}

/* ============================================
   Contact Form Handler
   ============================================ */
function handleContact(event) {
  event.preventDefault();
  
  const form = event.target;
  const btn = form.querySelector('.btn-primary');
  btn.textContent = 'Sending...';
  btn.disabled = true;
  
  setTimeout(() => {
    alert('Message sent successfully! (This is a demo)');
    form.reset();
    btn.textContent = 'Send Message';
    btn.disabled = false;
  }, 1500);
}

/* ============================================
   Dashboard Sidebar Toggle
   ============================================ */
function toggleDashboardSidebar() {
  const sidebar = document.querySelector('.dashboard-sidebar');
  if (sidebar) {
    sidebar.classList.toggle('active');
  }
}

/* ============================================
   Modal Functions
   ============================================ */
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
  if (event.target.classList.contains('modal')) {
    event.target.classList.remove('active');
    document.body.style.overflow = '';
  }
});

/* ============================================
   Star Rating
   ============================================ */
function generateStars(rating) {
  let stars = '';
  const fullStars = Math.floor(rating);
  const hasHalfStar = rating % 1 >= 0.5;
  
  for (let i = 0; i < fullStars; i++) {
    stars += '★';
  }
  
  if (hasHalfStar) {
    stars += '☆';
  }
  
  const emptyStars = 5 - Math.ceil(rating);
  for (let i = 0; i < emptyStars; i++) {
    stars += '☆';
  }
  
  return stars;
}

/* ============================================
   Smooth Scroll
============================================ */

document.querySelectorAll('a[href^="#"]').forEach(anchor => {

  anchor.addEventListener('click', function(e) {

    const href =
      this.getAttribute('href');

    if (
      !href ||
      href === '#' ||
      !href.startsWith('#')
    ) {
      return;
    }

    const target =
      document.querySelector(href);

    if (target) {

      e.preventDefault();

      const offsetTop =
        target.offsetTop - 80;

      window.scrollTo({
        top: offsetTop,
        behavior: 'smooth'
      });

    }

  });

});