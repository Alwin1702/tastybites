document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------
    // Recipe Database (Embedded for Static/Offline Client Mode)
    // -------------------------------------------------------------
    const defaultRecipes = {
        1: {
            id: 1,
            name: "Chocolate Cake",
            category: "Dessert",
            image: "https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600",
            cooking_time: 40,
            ingredients: ["Flour", "Sugar", "Cocoa Powder", "Eggs", "Milk", "Butter"],
            instructions: [
                "Mix all dry and wet ingredients thoroughly in a large bowl.",
                "Pour the batter evenly into a greased cake tin.",
                "Bake in a preheated oven at 180°C for 30 to 35 minutes.",
                "Let cool completely and top with rich chocolate glaze."
            ]
        },
        2: {
            id: 2,
            name: "Vegetable Salad",
            category: "Breakfast",
            image: "https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600",
            cooking_time: 10,
            ingredients: ["Fresh Lettuce", "Vine Tomatoes", "Crisp Cucumber", "Shredded Carrot", "Extra Virgin Olive Oil", "Sea Salt & Black Pepper"],
            instructions: [
                "Thoroughly wash and pat dry the crisp vegetables.",
                "Chop lettuce, slice cucumbers and tomatoes into bite sizes.",
                "Toss together in a salad bowl with olive oil, salt, and black pepper."
            ]
        },
        3: {
            id: 3,
            name: "Pasta",
            category: "Lunch",
            image: "https://images.unsplash.com/photo-1473093295043-cdd812d0e601?w=600",
            cooking_time: 25,
            ingredients: ["Pasta", "Tomato Sauce", "Onion", "Garlic", "Olive Oil", "Fresh Basil", "Salt"],
            instructions: [
                "Boil pasta in a pot of salted water until al dente.",
                "Sauté minced garlic and diced onions in olive oil until golden fragrant.",
                "Add tomato sauce, simmer, and toss together with the cooked pasta."
            ]
        },
        4: {
            id: 4,
            name: "Pancakes",
            category: "Breakfast",
            image: "https://images.unsplash.com/photo-1528207776546-365bb710ee93?w=600",
            cooking_time: 15,
            ingredients: ["All-Purpose Flour", "Whole Milk", "Farm Egg", "Cane Sugar", "Baking Powder", "Pure Maple Syrup"],
            instructions: [
                "Whisk flour, milk, egg, sugar, and baking powder until smooth batter forms.",
                "Pour a ladle of batter onto a hot, greased non-stick skillet.",
                "Cook until bubbles burst on surface, flip, and cook until golden brown."
            ]
        },
        5: {
            id: 5,
            name: "Chicken Burger",
            category: "Dinner",
            image: "https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600",
            cooking_time: 30,
            ingredients: ["Brioche Burger Bun", "Chicken Patty", "Crisp Lettuce", "Sliced Tomato", "Cheddar Cheese", "Special Burger Sauce"],
            instructions: [
                "Grill or sear the seasoned chicken patty until thoroughly cooked and juicy.",
                "Lightly toast the brioche buns on the pan.",
                "Layer patty, melted cheese, crisp lettuce, tomato, and sauce into the bun."
            ]
        },
        6: {
            id: 6,
            name: "Fruit Smoothie",
            category: "Drinks",
            image: "https://images.unsplash.com/photo-1502741224143-90386d7f8c82?w=600",
            cooking_time: 5,
            ingredients: ["Ripe Banana", "Fresh Strawberries", "Chilled Milk", "Raw Honey", "Ice Cubes"],
            instructions: [
                "Peel the banana and hull the fresh strawberries.",
                "Add fruit, chilled milk, honey, and ice into a blender.",
                "Blend on high for 60 seconds until creamy and serve chilled."
            ]
        }
    };

    // Load any custom recipes stored in localStorage
    const CUSTOM_RECIPES_KEY = 'tastybites_custom_recipes';
    function getStoredRecipes() {
        try {
            const custom = JSON.parse(localStorage.getItem(CUSTOM_RECIPES_KEY)) || {};
            return { ...defaultRecipes, ...custom };
        } catch {
            return defaultRecipes;
        }
    }

    // -------------------------------------------------------------
    // Utility: Toast Notification
    // -------------------------------------------------------------
    function showToast(message, icon = '✨') {
        const existing = document.querySelector('.toast-notice');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'toast-notice';
        toast.innerHTML = `<span style="font-size:20px; line-height:1;">${icon}</span> <span>${message}</span>`;
        document.body.appendChild(toast);

        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.animation = 'toastOut 0.4s ease forwards';
                setTimeout(() => toast.remove(), 400);
            }
        }, 3200);
    }

    // -------------------------------------------------------------
    // 0. Spiral Dishes Intro Controller
    // -------------------------------------------------------------
    const introOverlay = document.getElementById('introOverlay');
    const skipIntroBtn = document.getElementById('skipIntroBtn');

    if (introOverlay) {
        let introTimer = null;
        let removeTimer = null;

        const completeIntro = () => {
            sessionStorage.setItem('tastybites_intro_seen', 'true');
            introOverlay.classList.add('fade-out');
            if (introTimer) clearTimeout(introTimer);
            if (removeTimer) clearTimeout(removeTimer);
            removeTimer = setTimeout(() => {
                introOverlay.style.display = 'none';
            }, 600);
        };

        if (sessionStorage.getItem('tastybites_intro_seen') === 'true') {
            introOverlay.style.display = 'none';
        } else {
            introTimer = setTimeout(completeIntro, 5200);

            if (skipIntroBtn) {
                skipIntroBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    completeIntro();
                });
            }

            introOverlay.addEventListener('click', completeIntro);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' || e.key === ' ') completeIntro();
            });
        }
    }

    // -------------------------------------------------------------
    // 1. Search and Category Filter Controller
    // -------------------------------------------------------------
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const categoryPills = document.querySelectorAll('.category-pill');
    const noResults = document.getElementById('noResults');
    const recipeCountBadge = document.getElementById('recipeCountBadge');

    let currentCategory = 'all';
    let isFavoritesMode = false;

    function filterRecipes() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const allCards = document.querySelectorAll('.recipe-card');
        const favs = getLocalFavorites();
        let visibleCount = 0;

        allCards.forEach((card) => {
            const cardId = card.dataset.id || '';
            const name = (card.dataset.name || '').toLowerCase();
            const category = (card.dataset.category || '').toLowerCase();
            const ingredients = (card.dataset.ingredients || '').toLowerCase();
            const cardText = card.textContent.toLowerCase();

            const matchesSearch = !query ||
                name.includes(query) ||
                ingredients.includes(query) ||
                category.includes(query) ||
                cardText.includes(query);

            const matchesCategory = currentCategory === 'all' ||
                category === currentCategory.toLowerCase();

            const matchesFavMode = !isFavoritesMode || favs.includes(String(cardId));

            if (matchesSearch && matchesCategory && matchesFavMode) {
                card.style.display = 'flex';
                card.style.animation = 'none';
                void card.offsetWidth;
                const delay = Math.min(visibleCount * 0.04, 0.35);
                card.style.animation = `cardEntrance 0.35s cubic-bezier(0.16, 1, 0.3, 1) ${delay}s both`;
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (noResults) {
            noResults.style.display = (visibleCount === 0 && !isFavoritesMode) ? 'block' : 'none';
        }

        const emptyFavoritesState = document.getElementById('emptyFavoritesState');
        if (emptyFavoritesState) {
            emptyFavoritesState.style.display = (isFavoritesMode && visibleCount === 0) ? 'block' : 'none';
        }

        if (recipeCountBadge) {
            recipeCountBadge.textContent = `${visibleCount} ${visibleCount === 1 ? 'Recipe' : 'Recipes'}`;
        }
    }

    if (categoryPills.length > 0) {
        categoryPills.forEach(pill => {
            pill.addEventListener('click', () => {
                categoryPills.forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                currentCategory = (pill.dataset.cat || 'all').toLowerCase();
                if (categoryFilter) categoryFilter.value = pill.dataset.cat;
                filterRecipes();
            });
        });
    }

    if (categoryFilter) {
        categoryFilter.addEventListener('change', () => {
            currentCategory = (categoryFilter.value || 'all').toLowerCase();
            categoryPills.forEach(p => {
                p.classList.toggle('active', (p.dataset.cat || '').toLowerCase() === currentCategory);
            });
            filterRecipes();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterRecipes);
    }

    // -------------------------------------------------------------
    // 2. Favorites Management (LocalStorage + AJAX Hybrid)
    // -------------------------------------------------------------
    const LOCAL_FAVS_KEY = 'tastybites_saved_favorites';
    function getLocalFavorites() {
        try {
            return JSON.parse(localStorage.getItem(LOCAL_FAVS_KEY)) || [];
        } catch {
            return [];
        }
    }

    function toggleLocalFavorite(recipeId) {
        const favs = getLocalFavorites();
        const id = String(recipeId);
        const index = favs.indexOf(id);
        let isFav = false;
        if (index > -1) {
            favs.splice(index, 1);
            isFav = false;
        } else {
            favs.push(id);
            isFav = true;
        }
        localStorage.setItem(LOCAL_FAVS_KEY, JSON.stringify(favs));
        return isFav;
    }

    function setLocalFavorite(recipeId, isFav) {
        const favs = getLocalFavorites();
        const id = String(recipeId);
        const index = favs.indexOf(id);
        if (isFav && index === -1) {
            favs.push(id);
        } else if (!isFav && index > -1) {
            favs.splice(index, 1);
        }
        localStorage.setItem(LOCAL_FAVS_KEY, JSON.stringify(favs));
    }

    // -------------------------------------------------------------
    // 3. Standalone Client-Side Mode Interceptor (for index.html & GitHub Pages)
    // -------------------------------------------------------------
    const appModeMeta = document.querySelector('meta[name="app-mode"]');
    const isStaticHtml = (appModeMeta && appModeMeta.content === 'static') ||
                         Boolean(document.getElementById('recipeModal')) ||
                         window.location.protocol === 'file:' || 
                         window.location.hostname.includes('github.io');

    // Sync button visual state with favorite status
    function syncAllFavoriteButtons() {
        const favs = getLocalFavorites();
        document.querySelectorAll('.favorite-icon-btn').forEach(btn => {
            // Never alter remove-mode buttons on the dedicated favorites page
            if (btn.classList.contains('remove-mode')) {
                return;
            }
            const card = btn.closest('.recipe-card');
            const id = btn.dataset.id || (card ? card.dataset.id : null);
            if (id && favs.includes(String(id))) {
                btn.textContent = '❤️';
                btn.classList.add('is-active');
                btn.title = 'Remove from favorites';
            } else {
                btn.textContent = '🤍';
                btn.classList.remove('is-active');
                btn.title = 'Add to favorites';
            }
        });
    }

    // In static mode, sync button UI from localStorage; in PHP dynamic mode, preserve MySQL state and sync localStorage from DOM
    if (isStaticHtml) {
        syncAllFavoriteButtons();
    } else {
        const serverFavs = [];
        document.querySelectorAll('.recipe-card').forEach(card => {
            const favBtn = card.querySelector('.favorite-icon-btn');
            if (favBtn && favBtn.classList.contains('is-active') && card.dataset.id) {
                serverFavs.push(String(card.dataset.id));
            }
        });
        if (serverFavs.length > 0) {
            localStorage.setItem(LOCAL_FAVS_KEY, JSON.stringify(serverFavs));
        }
    }


    const navHome = document.getElementById('navHome');
    const navFavorites = document.getElementById('navFavorites');
    const navAddRecipe = document.getElementById('navAddRecipe');
    const brandLogo = document.getElementById('brandLogo');
    const sectionTitle = document.getElementById('sectionTitle');
    const sectionSub = document.getElementById('sectionSub');
    const browseAllRecipesBtn = document.getElementById('browseAllRecipesBtn');

    function switchToHome() {
        isFavoritesMode = false;
        if (navHome) navHome.classList.add('active');
        if (navFavorites) navFavorites.classList.remove('active');
        if (sectionTitle) sectionTitle.textContent = 'Explore Recipes';
        if (sectionSub) sectionSub.style.display = 'none';
        filterRecipes();
    }

    function switchToFavorites() {
        isFavoritesMode = true;
        if (navFavorites) navFavorites.classList.add('active');
        if (navHome) navHome.classList.remove('active');
        if (sectionTitle) sectionTitle.textContent = 'My Saved Favorites ❤️';
        if (sectionSub) sectionSub.style.display = 'block';
        filterRecipes();
    }

    if (isStaticHtml) {
        if (navFavorites) {
            navFavorites.addEventListener('click', (e) => {
                e.preventDefault();
                switchToFavorites();
            });
        }

        if (navHome) {
            navHome.addEventListener('click', (e) => {
                e.preventDefault();
                switchToHome();
            });
        }

        if (brandLogo) {
            brandLogo.addEventListener('click', (e) => {
                e.preventDefault();
                switchToHome();
            });
        }

        if (browseAllRecipesBtn) {
            browseAllRecipesBtn.addEventListener('click', switchToHome);
        }
    }

    // -------------------------------------------------------------
    // 4. Recipe Details Modal (Client-Side)
    // -------------------------------------------------------------
    const recipeModal = document.getElementById('recipeModal');
    const closeRecipeModalBtn = document.getElementById('closeRecipeModalBtn');
    const modalBackLink = document.getElementById('modalBackLink');
    const modalRecipeImage = document.getElementById('modalRecipeImage');
    const modalRecipeName = document.getElementById('modalRecipeName');
    const modalRecipeCategory = document.getElementById('modalRecipeCategory');
    const modalRecipeTime = document.getElementById('modalRecipeTime');
    const modalIngredientsList = document.getElementById('modalIngredientsList');
    const modalInstructionSteps = document.getElementById('modalInstructionSteps');
    const modalFavToggleBtn = document.getElementById('modalFavToggleBtn');

    let activeModalRecipeId = null;

    function openRecipeDetails(recipeId) {
        const allRecipes = getStoredRecipes();
        const recipe = allRecipes[recipeId];
        if (!recipe || !recipeModal) return;

        activeModalRecipeId = recipeId;
        const favs = getLocalFavorites();
        const isFav = favs.includes(String(recipeId));

        if (modalRecipeImage) modalRecipeImage.src = recipe.image || 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=600';
        if (modalRecipeName) modalRecipeName.textContent = recipe.name;
        if (modalRecipeCategory) modalRecipeCategory.textContent = `🏷️ ${recipe.category}`;
        if (modalRecipeTime) modalRecipeTime.textContent = `⏱️ ${recipe.cooking_time} minutes`;

        // Render ingredients checklist
        if (modalIngredientsList) {
            modalIngredientsList.innerHTML = '';
            (recipe.ingredients || []).forEach(ing => {
                const li = document.createElement('li');
                li.className = 'ingredient-item';
                li.innerHTML = `<input type="checkbox" class="ingredient-checkbox"> <span>${ing}</span>`;
                li.addEventListener('click', (e) => {
                    const cb = li.querySelector('.ingredient-checkbox');
                    if (e.target !== cb) cb.checked = !cb.checked;
                    li.classList.toggle('checked', cb.checked);
                });
                modalIngredientsList.appendChild(li);
            });
        }

        // Render instruction step cards
        if (modalInstructionSteps) {
            modalInstructionSteps.innerHTML = '';
            (recipe.instructions || []).forEach((step, idx) => {
                const div = document.createElement('div');
                div.className = 'step-card';
                div.innerHTML = `<div class="step-number">${idx + 1}</div><div class="step-text">${step}</div>`;
                modalInstructionSteps.appendChild(div);
            });
        }

        // Update modal favorite button
        if (modalFavToggleBtn) {
            if (isFav) {
                modalFavToggleBtn.textContent = '💔 Remove from Favorites';
                modalFavToggleBtn.style.background = '#64748B';
            } else {
                modalFavToggleBtn.textContent = '❤️ Save to Favorites';
                modalFavToggleBtn.style.background = 'linear-gradient(135deg, #FF5A36, #E44825)';
            }
        }

        recipeModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeRecipeDetails() {
        if (!recipeModal) return;
        recipeModal.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (closeRecipeModalBtn) closeRecipeModalBtn.addEventListener('click', closeRecipeDetails);
    if (modalBackLink) modalBackLink.addEventListener('click', closeRecipeDetails);
    if (recipeModal) {
        recipeModal.addEventListener('click', (e) => {
            if (e.target === recipeModal) closeRecipeDetails();
        });
    }

    if (modalFavToggleBtn) {
        modalFavToggleBtn.addEventListener('click', () => {
            if (!activeModalRecipeId) return;
            const isNowFav = toggleLocalFavorite(activeModalRecipeId);
            if (isNowFav) {
                modalFavToggleBtn.textContent = '💔 Remove from Favorites';
                modalFavToggleBtn.style.background = '#64748B';
                showToast('Saved to your favorites!', '❤️');
            } else {
                modalFavToggleBtn.textContent = '❤️ Save to Favorites';
                modalFavToggleBtn.style.background = 'linear-gradient(135deg, #FF5A36, #E44825)';
                showToast('Removed from favorites', '🤍');
            }
            syncAllFavoriteButtons();
            if (isFavoritesMode) filterRecipes();
        });
    }

    // Attach click listener to "View Recipe" buttons
    document.addEventListener('click', (e) => {
        const viewBtn = e.target.closest('.view-btn');
        if (viewBtn && recipeModal) {
            const card = viewBtn.closest('.recipe-card');
            const id = viewBtn.dataset.id || (card ? card.dataset.id : null);
            if (id && isStaticHtml) {
                e.preventDefault();
                openRecipeDetails(id);
            }
        }
    });

    // -------------------------------------------------------------
    // 5. Add Recipe Modal (Client-Side)
    // -------------------------------------------------------------
    const addRecipeModal = document.getElementById('addRecipeModal');
    const closeAddRecipeModalBtn = document.getElementById('closeAddRecipeModalBtn');
    const standaloneAddRecipeForm = document.getElementById('standaloneAddRecipeForm');
    const recipeImageInput = document.getElementById('recipeImageInput');
    const recipeImagePreviewBox = document.getElementById('recipeImagePreviewBox');

    function openAddRecipeModal() {
        if (!addRecipeModal) return;
        addRecipeModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeAddRecipeModal() {
        if (!addRecipeModal) return;
        addRecipeModal.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (isStaticHtml && navAddRecipe) {
        navAddRecipe.addEventListener('click', (e) => {
            e.preventDefault();
            openAddRecipeModal();
        });
    }

    if (closeAddRecipeModalBtn) closeAddRecipeModalBtn.addEventListener('click', closeAddRecipeModal);
    if (addRecipeModal) {
        addRecipeModal.addEventListener('click', (e) => {
            if (e.target === addRecipeModal) closeAddRecipeModal();
        });
    }

    if (recipeImageInput && recipeImagePreviewBox) {
        recipeImageInput.addEventListener('input', () => {
            const url = recipeImageInput.value.trim();
            if (!url) {
                recipeImagePreviewBox.innerHTML = 'Live image preview will appear here';
                return;
            }
            const img = new Image();
            img.onload = () => {
                recipeImagePreviewBox.innerHTML = '';
                recipeImagePreviewBox.appendChild(img);
            };
            img.onerror = () => {
                recipeImagePreviewBox.innerHTML = '<span style="color:#ef4444; font-weight:600;">⚠️ Could not load image preview. Please check URL.</span>';
            };
            img.src = url;
        });
    }

    if (standaloneAddRecipeForm) {
        standaloneAddRecipeForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('recipeNameInput').value.trim();
            const category = document.getElementById('recipeCategoryInput').value.trim();
            const image = document.getElementById('recipeImageInput').value.trim() || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600';
            const cooking_time = parseInt(document.getElementById('recipeCookingTimeInput').value, 10) || 20;
            const rawIngredients = document.getElementById('recipeIngredientsInput').value.trim();
            const rawInstructions = document.getElementById('recipeInstructionsInput').value.trim();

            const ingredients = rawIngredients.split(/[,\n]/).map(s => s.trim()).filter(Boolean);
            const instructions = rawInstructions.split(/[.\n]/).map(s => s.trim()).filter(Boolean);

            const newId = Date.now();
            const newRecipe = {
                id: newId,
                name,
                category,
                image,
                cooking_time,
                ingredients,
                instructions
            };

            // Save into custom recipes in localStorage
            try {
                const custom = JSON.parse(localStorage.getItem(CUSTOM_RECIPES_KEY)) || {};
                custom[newId] = newRecipe;
                localStorage.setItem(CUSTOM_RECIPES_KEY, JSON.stringify(custom));
            } catch (err) {
                console.error(err);
            }

            // Create new recipe card element and add to grid
            const recipeGrid = document.getElementById('recipeGrid');
            if (recipeGrid) {
                const card = document.createElement('div');
                card.className = 'recipe-card';
                card.dataset.id = newId;
                card.dataset.name = name.toLowerCase();
                card.dataset.category = category;
                card.dataset.ingredients = ingredients.join(', ').toLowerCase();

                card.innerHTML = `
                    <div class="card-media">
                        <span class="card-badge" data-cat="${category}">${category}</span>
                        <img src="${image}" alt="${name}" loading="lazy">
                    </div>
                    <div class="card-content">
                        <h3>${name}</h3>
                        <div class="recipe-meta">
                            <span class="meta-item">⏱️ ${cooking_time} mins</span>
                            <span class="meta-item">🍽️ ${category}</span>
                        </div>
                        <div class="card-actions">
                            <a class="view-btn" href="#recipe-${newId}" data-id="${newId}">View Recipe &rarr;</a>
                            <button type="button" class="favorite-icon-btn" data-id="${newId}" title="Add to favorites">🤍</button>
                        </div>
                    </div>
                `;
                recipeGrid.prepend(card);
            }

            standaloneAddRecipeForm.reset();
            if (recipeImagePreviewBox) recipeImagePreviewBox.innerHTML = 'Live image preview will appear here';
            closeAddRecipeModal();
            showToast('Recipe published successfully! 🚀', '🎉');
            switchToHome();
        });
    }

    // -------------------------------------------------------------
    // 6. Standalone Favorite Buttons Click Handler
    // -------------------------------------------------------------
    document.addEventListener('click', (e) => {
        const favBtn = e.target.closest('.favorite-icon-btn:not(form .favorite-icon-btn)');
        if (favBtn) {
            e.preventDefault();
            favBtn.style.transform = 'scale(1.35)';
            setTimeout(() => { favBtn.style.transform = ''; }, 220);

            const card = favBtn.closest('.recipe-card');
            const id = favBtn.dataset.id || (card ? card.dataset.id : null);
            if (id) {
                const isNowFav = toggleLocalFavorite(id);
                if (isNowFav) {
                    favBtn.textContent = '❤️';
                    favBtn.classList.add('is-active');
                    favBtn.title = 'Remove from favorites';
                    showToast('Added to favorites!', '❤️');
                } else {
                    favBtn.textContent = '🤍';
                    favBtn.classList.remove('is-active');
                    favBtn.title = 'Add to favorites';
                    showToast('Removed from favorites', '🤍');
                }
                if (isFavoritesMode) filterRecipes();
            }
        }
    });

    // -------------------------------------------------------------
    // 7. PHP Form AJAX Handling (When running on index.php)
    // -------------------------------------------------------------
    const favoriteForms = document.querySelectorAll('form[action="favorite_action.php"], .favorite-form');
    favoriteForms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = form.querySelector('.favorite-icon-btn, .recipe-detail-fav-btn, button[type="submit"]');
            const recipeIdInput = form.querySelector('input[name="recipe_id"]');
            const actionInput = form.querySelector('input[name="action"]');
            const recipeCard = form.closest('.recipe-card');

            if (submitBtn) {
                submitBtn.style.transform = 'scale(1.35)';
                setTimeout(() => { submitBtn.style.transform = ''; }, 220);
            }

            const currentAction = actionInput ? actionInput.value : 'add';
            const formData = new FormData(form);
            formData.append('ajax', '1');

            let isSuccess = false;
            let nextIsFav = (currentAction === 'add');

            try {
                const res = await fetch('favorite_action.php', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (res.ok) {
                    const data = await res.json();
                    if (data.success) {
                        isSuccess = true;
                        nextIsFav = data.is_favorite;
                    }
                }
            } catch (err) {
                if (recipeIdInput) {
                    nextIsFav = toggleLocalFavorite(recipeIdInput.value);
                    isSuccess = true;
                }
            }

            if (isSuccess && submitBtn) {
                if (recipeIdInput) {
                    setLocalFavorite(recipeIdInput.value, nextIsFav);
                }
                if (submitBtn.classList.contains('remove-mode')) {
                    if (recipeCard) {
                        recipeCard.style.transition = 'all 0.35s cubic-bezier(0.16, 1, 0.3, 1)';
                        recipeCard.style.opacity = '0';
                        recipeCard.style.transform = 'scale(0.85) translateY(20px)';
                        setTimeout(() => {
                            recipeCard.remove();
                            const remainingCards = document.querySelectorAll('.recipe-grid .recipe-card');
                            const countBadge = document.querySelector('.recipe-count-badge');
                            if (countBadge) {
                                countBadge.textContent = `${remainingCards.length} ${remainingCards.length === 1 ? 'Favorite' : 'Favorites'}`;
                            }
                            if (remainingCards.length === 0) {
                                const grid = document.querySelector('.recipe-grid');
                                if (grid) grid.style.display = 'none';
                                const emptyBox = document.querySelector('.empty-favorites-box');
                                if (emptyBox) {
                                    emptyBox.style.display = 'block';
                                }
                            }
                        }, 350);
                    }
                    showToast('Removed from favorites', '💔');
                } else if (submitBtn.classList.contains('recipe-detail-fav-btn')) {
                    if (nextIsFav) {
                        submitBtn.innerHTML = '💔 Remove from Favorites';
                        submitBtn.style.background = '#64748B';
                        submitBtn.classList.add('is-active');
                        if (actionInput) actionInput.value = 'remove';
                        showToast('Saved to your favorites!', '❤️');
                    } else {
                        submitBtn.innerHTML = '❤️ Save to Favorites';
                        submitBtn.style.background = 'linear-gradient(135deg, #FF5A36, #E44825)';
                        submitBtn.classList.remove('is-active');
                        if (actionInput) actionInput.value = 'add';
                        showToast('Removed from favorites', '🤍');
                    }
                } else {
                    if (nextIsFav) {
                        submitBtn.textContent = '❤️';
                        submitBtn.classList.add('is-active');
                        submitBtn.title = 'Remove from favorites';
                        if (actionInput) actionInput.value = 'remove';
                        showToast('Added to favorites!', '❤️');
                    } else {
                        submitBtn.textContent = '🤍';
                        submitBtn.classList.remove('is-active');
                        submitBtn.title = 'Add to favorites';
                        if (actionInput) actionInput.value = 'add';
                        showToast('Removed from favorites', '🤍');
                    }
                }
            }
        });
    });

    // -------------------------------------------------------------
    // 8. Server-Side Recipe Page Checklist (recipe.php)
    // -------------------------------------------------------------
    const ingredientItems = document.querySelectorAll('.ingredient-item:not(#modalIngredientsList .ingredient-item)');
    ingredientItems.forEach(item => {
        const checkbox = item.querySelector('.ingredient-checkbox');
        if (!checkbox) return;
        checkbox.addEventListener('change', () => {
            item.classList.toggle('checked', checkbox.checked);
        });
        item.addEventListener('click', (e) => {
            if (e.target !== checkbox) {
                checkbox.checked = !checkbox.checked;
                item.classList.toggle('checked', checkbox.checked);
            }
        });
    });

    // -------------------------------------------------------------
    // 9. Server-Side Image Preview (add_recipe.php)
    // -------------------------------------------------------------
    const imageUrlInput = document.getElementById('image');
    const imagePreviewBox = document.getElementById('imagePreview');
    if (imageUrlInput && imagePreviewBox) {
        const updatePreview = () => {
            const url = imageUrlInput.value.trim();
            if (!url) {
                imagePreviewBox.innerHTML = 'Live image preview will appear here';
                return;
            }
            const img = new Image();
            img.onload = () => {
                imagePreviewBox.innerHTML = '';
                imagePreviewBox.appendChild(img);
            };
            img.onerror = () => {
                imagePreviewBox.innerHTML = '<span style="color:#ef4444; font-weight:600;">⚠️ Could not load image preview. Please check URL.</span>';
            };
            img.src = url;
        };
        imageUrlInput.addEventListener('input', updatePreview);
        if (imageUrlInput.value) updatePreview();
    }
});
