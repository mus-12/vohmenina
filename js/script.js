document.addEventListener('DOMContentLoaded', function () {

    // ===== BURGER MENU =====
    const burgerBtn = document.getElementById('burgerBtn');
    const navMenu = document.getElementById('navMenu');

    if (burgerBtn && navMenu) {
        burgerBtn.addEventListener('click', function () {
            navMenu.classList.toggle('open');
            this.textContent = navMenu.classList.contains('open') ? '✕' : '☰';
        });
    }

    // ===== ADD TO CART =====
    document.querySelectorAll('.add-to-cart-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);
            const image = this.dataset.image;
            const quantityInput = document.querySelector('.quantity-selector input');
            const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;

            fetch('api/cart_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=add&id=${id}&name=${encodeURIComponent(name)}&price=${price}&image=${encodeURIComponent(image)}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Товар добавлен в корзину!');
                    updateCartCount(data.cartCount);
                }
            })
            .catch(err => console.error('Error:', err));
        });
    });

    // ===== CART QUANTITY =====
    document.querySelectorAll('.cart-qty-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const action = this.dataset.action;
            const qtySpan = document.getElementById('qty-' + id);
            let qty = parseInt(qtySpan.textContent);

            if (action === 'increase') qty++;
            else if (action === 'decrease' && qty > 1) qty--;

            fetch('api/cart_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=update&id=${id}&quantity=${qty}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(err => console.error('Error:', err));
        });
    });

    // ===== REMOVE FROM CART =====
    document.querySelectorAll('.remove-cart-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;

            fetch('api/cart_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=remove&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(err => console.error('Error:', err));
        });
    });

    // ===== QUANTITY SELECTOR (product page) =====
    document.querySelectorAll('.qty-minus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = this.nextElementSibling;
            let val = parseInt(input.value) || 1;
            if (val > 1) input.value = val - 1;
        });
    });

    document.querySelectorAll('.qty-plus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = this.previousElementSibling;
            let val = parseInt(input.value) || 1;
            const max = parseInt(input.max) || 99;
            if (val < max) input.value = val + 1;
        });
    });

    // ===== TOAST =====
    function showToast(message) {
        let toast = document.querySelector('.toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'toast';
            document.body.appendChild(toast);
        }
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(function () {
            toast.classList.remove('show');
        }, 3000);
    }

    function updateCartCount(count) {
        const el = document.getElementById('cartCount');
        if (el) el.textContent = count;
    }

    // ===== FORM VALIDATION =====
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (e) {
            let valid = true;
            const required = this.querySelectorAll('[required]');

            required.forEach(function (field) {
                field.classList.remove('error');
                const errorEl = field.parentElement.querySelector('.form-error');
                if (errorEl) errorEl.remove();

                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('error');
                    field.style.borderColor = 'var(--danger)';
                    const error = document.createElement('div');
                    error.className = 'form-error';
                    error.textContent = 'Это поле обязательно';
                    field.parentElement.appendChild(error);
                } else {
                    field.style.borderColor = '';
                }
            });

            const email = this.querySelector('[type="email"]');
            if (email && email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                valid = false;
                email.style.borderColor = 'var(--danger)';
                let error = email.parentElement.querySelector('.form-error');
                if (!error) {
                    error = document.createElement('div');
                    error.className = 'form-error';
                    email.parentElement.appendChild(error);
                }
                error.textContent = 'Введите корректный email';
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }

    // // ===== CONTACT FORM =====
    // const contactForm = document.getElementById('contactForm');
    // if (contactForm) {
    //     contactForm.addEventListener('submit', function (e) {
    //         e.preventDefault();

    //         const formData = new FormData(this);
    //         const data = new URLSearchParams(formData);
    //         data.append('action', 'contact');

    //         fetch('../contacts.php', {
    //             method: 'POST',
    //             headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    //             body: data
    //         })
    //         .then(response => response.json())
    //         .then(function (result) {
    //             if (result.success) {
    //                 showToast('Сообщение отправлено! Мы свяжемся с вами.');
    //                 contactForm.reset();
    //             }
    //         })
    //         .catch(function (err) {
    //             console.error('Error:', err);
    //         });
    //     });
    // }

    // ===== SEARCH =====
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const query = this.value.toLowerCase();
                document.querySelectorAll('.product-card').forEach(function (card) {
                    const title = card.querySelector('.product-card-title').textContent.toLowerCase();
                    const cat = card.querySelector('.product-card-category').textContent.toLowerCase();
                    if (title.includes(query) || cat.includes(query) || query === '') {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }, 300);
        });
    }

    // ===== PRICE FILTER =====
    const priceMin = document.getElementById('priceMin');
    const priceMax = document.getElementById('priceMax');
    const priceFilterBtn = document.getElementById('priceFilterBtn');

    if (priceFilterBtn) {
        priceFilterBtn.addEventListener('click', function () {
            const min = parseFloat(priceMin.value) || 0;
            const max = parseFloat(priceMax.value) || Infinity;

            document.querySelectorAll('.product-card').forEach(function (card) {
                const priceText = card.querySelector('.price-current').textContent.replace(/\s/g, '').replace('₽', '');
                const price = parseFloat(priceText);
                if (price >= min && price <= max) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // ===== SMOOTH SCROLL FOR ANCHORS =====
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ===== ANIMATE ON SCROLL =====
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.product-card, .feature-card, .stat-card').forEach(function (el) {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });

});