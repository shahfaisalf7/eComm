var Header = (function () {
    return {
        form: { query: "" },
        suggestions: {
            categories: [],
            products: [],
            remaining: 0,
        },
        csrf_token: null,
        suggestionsBox: null,

        fetchSuggestions: async function () {
            if (!this.form.query.trim()) return;

            try {
                const url = '/suggestions?query=' + encodeURIComponent(this.form.query);

                const { data } = await axios.get(url, {
                    headers: { 'X-CSRF-TOKEN': this.csrf_token },
                });

                this.updateSuggestions(data);
            } catch (error) {
                console.error("Error fetching suggestions:", error);
                this.showError("Unable to fetch suggestions. Please try again.");
            }
        },

        init: function (csrf_token, suggestionsBoxSelector) {
            this.csrf_token = csrf_token;
            this.suggestionsBox = document.querySelector(suggestionsBoxSelector);

            if (!this.suggestionsBox) {
                console.error("Suggestions box not found. Ensure correct selector.");
                return;
            }

            this.attachGlobalListeners();
        },

        updateSuggestions: function (data) {
            if (!this.suggestionsBox) return;

            this.suggestionsBox.innerHTML = "";

            const fragment = document.createDocumentFragment();

            if (data.categories && data.categories.length > 0) {
                data.categories.forEach((category) => {
                    const categoryElement = document.createElement("div");
                    categoryElement.className = "category";
                    categoryElement.textContent = category.name;
                    categoryElement.onclick = () => (window.location.href = category.url);

                    fragment.appendChild(categoryElement);
                });
            }

            if (data.products && data.products.length > 0) {
                data.products.forEach((product) => {
                    const productElement = document.createElement("div");
                    productElement.className = "product";

                    productElement.innerHTML = `
                        <img src="${product.base_image.path}" alt="${product.name}" />
                        <div class="product-info">
                            <div>${product.name}</div>
                            <div>${product.formatted_price}</div>
                        </div>
                    `;
                    productElement.onclick = () => (window.location.href = product.url);

                    fragment.appendChild(productElement);
                });
            }

            this.suggestionsBox.appendChild(fragment);
            this.suggestionsBox.classList.remove("hidden");
        },

        attachGlobalListeners: function () {
            document.addEventListener("click", (event) => {
                if (!event.target.closest(".search-container")) {
                    this.hideSuggestionsBox();
                }
            });
        },

        hideSuggestionsBox: function () {
            if (this.suggestionsBox) {
                this.suggestionsBox.classList.add("hidden");
            }
        },

        showError: function (message) {
            if (this.suggestionsBox) {
                this.suggestionsBox.innerHTML = `<div class="error">${message}</div>`;
                this.suggestionsBox.classList.remove("hidden");
            }
        },
    };
})();

function renderSuggestions(data) {
    if (!Header.suggestionsBox) {
        console.error("Suggestions box not initialized.");
        return;
    }

    Header.updateSuggestions(data);
}
