/* ==========================================================================
   NASEEB CONSULTANT - MAIN INTERACTIVE JAVASCRIPT SYSTEM
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    // 1. STICKY HEADER & SCROLL EFFECTS
    const header = document.querySelector('.main-header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // 2. MOBILE NAVIGATION DRAWER
    const mobileToggle = document.querySelector('.mobile-nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    if (mobileToggle && navMenu) {
        mobileToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            // Animate toggle bars
            const spans = mobileToggle.querySelectorAll('span');
            spans[0].style.transform = navMenu.classList.contains('active') ? 'rotate(45deg) translate(6px, 6px)' : 'none';
            spans[1].style.opacity = navMenu.classList.contains('active') ? '0' : '1';
            spans[2].style.transform = navMenu.classList.contains('active') ? 'rotate(-45deg) translate(5px, -6px)' : 'none';
        });
    }

    // 3. WHATSAPP WIDGET PORTAL TRIGGER
    const waTrigger = document.getElementById('whatsapp-trigger');
    const waChatBox = document.getElementById('whatsapp-chat-box');
    if (waTrigger && waChatBox) {
        waTrigger.addEventListener('click', () => {
            waChatBox.classList.toggle('active');
        });
        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!waTrigger.contains(e.target) && !waChatBox.contains(e.target)) {
                waChatBox.classList.remove('active');
            }
        });
    }

    // 4. DYNAMIC SCROLL COUNTER ANIMATION
    const counterElements = document.querySelectorAll('.counter-val');
    if (counterElements.length > 0) {
        const startCounter = (el) => {
            const target = parseInt(el.getAttribute('data-target'), 10);
            let count = 0;
            const speed = target / 50; // duration is split
            const updateCount = () => {
                count += speed;
                if (count < target) {
                    el.innerText = Math.floor(count);
                    requestAnimationFrame(updateCount);
                } else {
                    el.innerText = target;
                }
            };
            updateCount();
        };

        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    startCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counterElements.forEach(el => counterObserver.observe(el));
    }

    // 5. TESTIMONIALS SLIDER AUTOMATION
    const slides = document.querySelectorAll('.testimonial-slide');
    const dots = document.querySelectorAll('.testimonials-wrapper .dot');
    if (slides.length > 0) {
        let currentSlide = 0;
        let slideInterval;

        const showSlide = (n) => {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            slides[n].classList.add('active');
            dots[n].classList.add('active');
            currentSlide = n;
        };

        const nextSlide = () => {
            let next = (currentSlide + 1) % slides.length;
            showSlide(next);
        };

        const startSlider = () => {
            slideInterval = setInterval(nextSlide, 6000);
        };

        const stopSlider = () => {
            clearInterval(slideInterval);
        };

        // Dots trigger clicks
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                stopSlider();
                showSlide(index);
                startSlider();
            });
        });

        // Initialize Slider
        showSlide(0);
        startSlider();
    }

    // 6. REAL-TIME COUNTRY FILTER (For destinations.html)
    const countrySearch = document.getElementById('country-search');
    const countryCards = document.querySelectorAll('.country-card');
    if (countrySearch) {
        countrySearch.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            countryCards.forEach(card => {
                const countryName = card.querySelector('.country-card-title').innerText.toLowerCase();
                const textDesc = card.innerText.toLowerCase();
                if (countryName.includes(query) || textDesc.includes(query)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // 7. STEP-BY-STEP ELIGIBILITY CALCULATOR (For services.html or home)
    const calcSteps = document.querySelectorAll('.calc-step');
    const btnNext = document.getElementById('calc-next');
    const btnPrev = document.getElementById('calc-prev');
    const btnRestart = document.getElementById('calc-restart');
    let currentCalcStep = 0;
    
    // Eligibility Variables
    let calculatorData = {
        degree: '',
        country: '',
        gpa: 3.0,
        englishScore: 6.5
    };

    const updateCalcView = () => {
        calcSteps.forEach((step, idx) => {
            step.classList.remove('active');
            if (idx === currentCalcStep) {
                step.classList.add('active');
            }
        });

        // Handle navigation buttons
        if (currentCalcStep === 0) {
            if (btnPrev) btnPrev.style.display = 'none';
            if (btnNext) btnNext.innerText = 'Next Question';
        } else if (currentCalcStep === calcSteps.length - 1) {
            // Reached result step
            if (btnPrev) btnPrev.style.display = 'none';
            if (btnNext) btnNext.style.display = 'none';
            runEligibilityCalculation();
        } else {
            if (btnPrev) btnPrev.style.display = 'inline-flex';
            if (btnNext) {
                btnNext.style.display = 'inline-flex';
                btnNext.innerText = currentCalcStep === calcSteps.length - 2 ? 'Compute Probability' : 'Next Question';
            }
        }
    };

    // Card/Option Box selections in Calculator
    document.querySelectorAll('.option-box').forEach(box => {
        box.addEventListener('click', function() {
            // Find sibling option boxes and clear selected class
            const parent = this.parentElement;
            parent.querySelectorAll('.option-box').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');

            // Store value
            const category = this.getAttribute('data-category');
            const val = this.getAttribute('data-value');
            if (category && val) {
                calculatorData[category] = val;
            }
        });
    });

    // Slider range selectors
    const gpaSlider = document.getElementById('calc-gpa');
    const gpaVal = document.getElementById('gpa-val');
    if (gpaSlider && gpaVal) {
        gpaSlider.addEventListener('input', (e) => {
            gpaVal.innerText = parseFloat(e.target.value).toFixed(2);
            calculatorData.gpa = parseFloat(e.target.value);
        });
    }

    const englishSlider = document.getElementById('calc-english');
    const englishVal = document.getElementById('english-val');
    if (englishSlider && englishVal) {
        englishSlider.addEventListener('input', (e) => {
            englishVal.innerText = parseFloat(e.target.value).toFixed(1);
            calculatorData.englishScore = parseFloat(e.target.value);
        });
    }

    if (btnNext) {
        btnNext.addEventListener('click', () => {
            // Validation before proceeding
            if (currentCalcStep === 0 && !calculatorData.degree) {
                showModalAlert('Please select your current qualification to proceed.');
                return;
            }
            if (currentCalcStep === 1 && !calculatorData.country) {
                showModalAlert('Please select your preferred study destination.');
                return;
            }

            if (currentCalcStep < calcSteps.length - 1) {
                currentCalcStep++;
                updateCalcView();
            }
        });
    }

    if (btnPrev) {
        btnPrev.addEventListener('click', () => {
            if (currentCalcStep > 0) {
                currentCalcStep--;
                updateCalcView();
            }
        });
    }

    if (btnRestart) {
        btnRestart.addEventListener('click', () => {
            currentCalcStep = 0;
            calculatorData = { degree: '', country: '', gpa: 3.0, englishScore: 6.5 };
            // Reset sliders
            if (gpaSlider) {
                gpaSlider.value = 3.0;
                gpaVal.innerText = '3.00';
            }
            if (englishSlider) {
                englishSlider.value = 6.5;
                englishVal.innerText = '6.5';
            }
            // Clear selected boxes
            document.querySelectorAll('.option-box').forEach(b => b.classList.remove('selected'));
            
            if (btnNext) btnNext.style.display = 'inline-flex';
            updateCalcView();
        });
    }

    // Eligibility computation engine
    const runEligibilityCalculation = () => {
        let score = 50; // base score

        // GPA influence (out of 4.0 scale)
        if (calculatorData.gpa >= 3.5) score += 25;
        else if (calculatorData.gpa >= 3.0) score += 15;
        else if (calculatorData.gpa >= 2.5) score += 5;
        else score -= 15;

        // English proficiency influence (IELTS equivalent out of 9)
        if (calculatorData.englishScore >= 7.5) score += 20;
        else if (calculatorData.englishScore >= 6.5) score += 10;
        else if (calculatorData.englishScore >= 5.5) score += 0;
        else score -= 20;

        // Country factor adjustment
        if (calculatorData.country === 'turkey' || calculatorData.country === 'russia') {
            score += 5;
        } else if (calculatorData.country === 'uk') {
            score -= 3;
        } else if (calculatorData.country === 'china') {
            score += 3;
        }

        // Clamp value between 15% and 98%
        const finalPercentage = Math.max(15, Math.min(98, score));

        // UI rendering
        const pctEl = document.getElementById('calc-result-pct');
        const verdictEl = document.getElementById('calc-result-verdict');
        const textEl = document.getElementById('calc-result-text');

        if (pctEl && verdictEl && textEl) {
            pctEl.innerText = `${finalPercentage}%`;
            
            // Set verdicts based on probability
            if (finalPercentage >= 85) {
                verdictEl.innerText = 'Excellent Eligibility';
                verdictEl.style.color = '#10b981';
                textEl.innerText = `Congratulations! You meet or exceed the typical entry requirements for your selected program in ${calculatorData.country.toUpperCase()}. You are a strong candidate for admission and scholarship programs.`;
            } else if (finalPercentage >= 65) {
                verdictEl.innerText = 'Good Eligibility';
                verdictEl.style.color = '#2563eb';
                textEl.innerText = `You have a very good chance of approval for ${calculatorData.country.toUpperCase()}. We recommend focusing on a solid statement of purpose and applying early.`;
            } else if (finalPercentage >= 45) {
                verdictEl.innerText = 'Moderate Eligibility';
                verdictEl.style.color = '#d97706';
                textEl.innerText = `You have moderate eligibility. You may need to boost your academic profile, prepare thoroughly for language tests, or target specific universities that accept your current profile. Naseeb Consultant can guide you on these specific options.`;
            } else {
                verdictEl.innerText = 'Low Eligibility (Needs Consultation)';
                verdictEl.style.color = '#ef4444';
                textEl.innerText = `Your profile does not fully match the general standard entry scores. However, Naseeb Consultant has partnerships with foundation courses and pathway programs that can still secure your study visa. Contact us immediately to explore custom paths.`;
            }
        }
    };

    // 8. CUSTOM INQUIRY AND BOOKING FORM SUBMISSION
    const forms = document.querySelectorAll('.inquiry-form-element, .contact-form-element');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Get form values
            const nameInput = form.querySelector('[name="name"], #name');
            const emailInput = form.querySelector('[name="email"], #email');
            const phoneInput = form.querySelector('[name="phone"], #phone');
            
            if (nameInput && nameInput.value.trim() === '') {
                showModalAlert('Please fill in your name.');
                return;
            }
            if (phoneInput && phoneInput.value.trim() === '') {
                showModalAlert('Please provide your phone/mobile number.');
                return;
            }

            // Beautiful Success Modal
            showModalAlert(`
                <div style="text-align: center;">
                    <i class="fas fa-check-circle" style="font-size: 3.5rem; color: #10b981; margin-bottom: 1.5rem;"></i>
                    <h3 style="font-size: 1.6rem; color: #0c1b33; margin-bottom: 0.8rem;">Thank You, ${nameInput ? nameInput.value.trim() : 'Student'}!</h3>
                    <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6;">
                        Your visa inquiry and admission profile has been successfully sent to Naseeb Consultant. A senior academic counselor will review your record and contact you on WhatsApp or call within 24 hours.
                    </p>
                </div>
            `, true);

            form.reset();
        });
    });

    // 9. PREMIUM DIALOG POPUP SYSTEM
    function showModalAlert(htmlContent, isCustomContent = false) {
        // Remove existing overlay
        const existing = document.getElementById('premium-alert-overlay');
        if (existing) existing.remove();

        const overlay = document.createElement('div');
        overlay.id = 'premium-alert-overlay';
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(12, 27, 51, 0.6)';
        overlay.style.backdropFilter = 'blur(6px)';
        overlay.style.zIndex = '9999';
        overlay.style.display = 'flex';
        overlay.style.alignItems = 'center';
        overlay.style.justifyContent = 'center';
        overlay.style.opacity = '0';
        overlay.style.transition = 'opacity 0.3s ease';

        const box = document.createElement('div');
        box.style.backgroundColor = '#ffffff';
        box.style.borderRadius = '20px';
        box.style.padding = '2.5rem';
        box.style.maxWidth = '460px';
        box.style.width = '90%';
        box.style.boxShadow = '0 25px 50px -12px rgba(12, 27, 51, 0.25)';
        box.style.transform = 'scale(0.9)';
        box.style.transition = 'transform 0.3s ease';

        if (isCustomContent) {
            box.innerHTML = htmlContent;
        } else {
            box.innerHTML = `
                <div style="text-align: center;">
                    <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #d97706; margin-bottom: 1.2rem;"></i>
                    <p style="color: #1e293b; font-size: 1.05rem; font-weight: 500; margin-bottom: 1.8rem; line-height: 1.5;">${htmlContent}</p>
                </div>
            `;
        }

        const closeBtn = document.createElement('button');
        closeBtn.innerText = 'Close';
        closeBtn.className = 'btn btn-primary';
        closeBtn.style.width = '100%';
        closeBtn.style.marginTop = '1.5rem';
        closeBtn.style.padding = '0.8rem';
        closeBtn.addEventListener('click', () => {
            overlay.style.opacity = '0';
            box.style.transform = 'scale(0.9)';
            setTimeout(() => overlay.remove(), 300);
        });

        box.appendChild(closeBtn);
        overlay.appendChild(box);
        document.body.appendChild(overlay);

        // Animation trigger
        setTimeout(() => {
            overlay.style.opacity = '1';
            box.style.transform = 'scale(1)';
        }, 50);
    }
});
