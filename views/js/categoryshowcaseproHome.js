document.addEventListener('DOMContentLoaded', function () {
	const swiperContainers = document.querySelectorAll('.swiper-container')
	swiperContainers.forEach((container) => {
		let swiperId = container.getAttribute('data-swiper-id')
		let navigationPrev = document.querySelector(
			'.swiper-navigation[data-swiper-id="' +
				swiperId +
				'"] .swiper-button-prev'
		)
		let navigationNext = document.querySelector(
			'.swiper-navigation[data-swiper-id="' +
				swiperId +
				'"] .swiper-button-next'
		)

		const swiper = new Swiper(container, {
			direction: 'vertical',
			slidesPerView: 3,
			spaceBetween: 30,
			height: 410,
			loop: false,
			observer: true,
			observeParents: true,
			pagination: {
				el: container.querySelector('.swiper-pagination'),
				clickable: true,
			},
			navigation: {
				nextEl: navigationNext,
				prevEl: navigationPrev,
			},
		})

		let nextPage = 2

		const loadMoreProducts = (categoryId, slideTo) => {
			fetch(ajaxMoreProductsCategoryShowcasePro, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({
					page: nextPage,
					category: categoryId,
				}),
			})
				.then((response) => response.json())
				.then((products) => {
					if (products.length === 0) {
						// Brak więcej produktów
						return
					}
					products.forEach((product) => {
						addProductToSwiper(product.products)
					})
					nextPage++
					swiper.update()
					swiper.slideTo(slideTo)
					swiper.slideNext()
				})
				.catch((error) => {
					console.error('Error:', error)
				})
		}

		const addProductToSwiper = (allProducts) => {
			allProducts.forEach((product) => {
				// Tworzenie głównego kontenera dla slajdu
				const slide = document.createElement('div')
				slide.className = 'swiper-slide'
				console.log(product)
				// Tworzenie kontenera dla produktu
				const productDiv = document.createElement('div')
				productDiv.className = 'product'

				// Link do produktu
				const productLink = document.createElement('a')
				productLink.href = product.url
				productLink.className = 'thumbnail product-thumbnail'

				// Obrazek produktu
				const picture = document.createElement('picture')
				if (product.cover) {
					if (product.cover.bySize.home_default.sources.avif) {
						const sourceAvif = document.createElement('source')
						sourceAvif.srcset = product.cover.bySize.home_default.sources.avif
						sourceAvif.type = 'image/avif'
						picture.appendChild(sourceAvif)
					}
					if (product.cover.bySize.home_default.sources.webp) {
						const sourceWebp = document.createElement('source')
						sourceWebp.srcset = product.cover.bySize.home_default.sources.webp
						sourceWebp.type = 'image/webp'
						picture.appendChild(sourceWebp)
					}
					const img = document.createElement('img')
					img.src = product.cover.bySize.home_default.url
					img.alt = product.cover.legend || product.name
					img.loading = 'lazy'
					picture.appendChild(img)
				}
				productLink.appendChild(picture)
				productDiv.appendChild(productLink)

				// Informacje o produkcie
				const productInfo = document.createElement('div')
				productInfo.className = 'product-info'

				const truncateText = (text, maxLength) => {
					if (text.length > maxLength) {
						return text.slice(0, maxLength) + '...'
					} else {
						return text
					}
				}
				// Nazwa produktu
				const productName = document.createElement('span')
				productName.className = 'product-name'
				const productLinkName = document.createElement('a')
				productLinkName.href = product.url
				productLinkName.textContent = truncateText(product.name, 30)
				productName.appendChild(productLinkName)
				productInfo.appendChild(productName)

				// Cena produktu
				const priceBox = document.createElement('div')
				priceBox.className = 'price-box'
				const priceAndShipping = document.createElement('div')
				priceAndShipping.className = 'product-price-and-shipping'

				if (product.show_price) {
					if (product.has_discount) {
						const priceSpan = document.createElement('span')
						priceSpan.className = 'product-price'
						priceSpan.textContent = product.price
						priceAndShipping.appendChild(priceSpan)

						const discountSpan = document.createElement('span')
						discountSpan.className = 'discount-percentage discount-product'
						discountSpan.textContent =
							product.discountType === 'percentage'
								? product.discount_percentage
								: product.discount_amount_to_display
						priceAndShipping.appendChild(discountSpan)
					} else {
						const regularPriceSpan = document.createElement('span')
						regularPriceSpan.className = 'regular-price product-price'
						regularPriceSpan.textContent = product.regular_price
						priceAndShipping.appendChild(regularPriceSpan)
					}
				}

				// Formularz dodawania do koszyka
				const addToCartForm = document.createElement('form')
				addToCartForm.action = addToCart
				addToCartForm.method = 'post'
				addToCartForm.id = 'add-to-cart-or-refresh'

				const tokenInput = document.createElement('input')
				tokenInput.type = 'hidden'
				tokenInput.name = 'token'
				tokenInput.value = static_token
				addToCartForm.appendChild(tokenInput)

				const productIdInput = document.createElement('input')
				productIdInput.type = 'hidden'
				productIdInput.name = 'id_product'
				productIdInput.id = 'product_page_product_id'
				productIdInput.value = product.id
				addToCartForm.appendChild(productIdInput)

				// const productIdAttributeInput = document.createElement('input')
				// productIdAttributeInput.type = 'hidden'
				// productIdAttributeInput.name = 'id_product_attribute'
				// productIdAttributeInput.id = 'product_page_product_id_attribute'
				// productIdAttributeInput.value = product.id_product_attribute
				// 	? product.id_product_attribute
				// 	: null
				// addToCartForm.appendChild(productIdAttributeInput)

				const productIdCustomizationInput = document.createElement('input')
				productIdCustomizationInput.type = 'hidden'
				productIdCustomizationInput.name = 'id_product_customization'
				productIdCustomizationInput.id = 'product_customization_id'
				productIdCustomizationInput.className = 'js-product-customization-id'
				productIdCustomizationInput.value = product.id_customization
					? product.id_customization
					: 0
				addToCartForm.appendChild(productIdCustomizationInput)

				const productQuantityDiv = document.createElement('div')
				productQuantityDiv.className = 'product-quantity'

				const quantityInput = document.createElement('input')
				quantityInput.type = 'number'
				quantityInput.name = 'qty'
				quantityInput.setAttribute('value', '1')
				quantityInput.min = '1'
				productQuantityDiv.appendChild(quantityInput)

				const addToCartButton = document.createElement('button')
				addToCartButton.className = 'add-to-cart'
				addToCartButton.setAttribute('data-button-action', 'add-to-cart')
				addToCartButton.type = 'submit'
				addToCartButton.innerHTML =
					'<i class="material-icons shopping-cart"></i>'

				productQuantityDiv.appendChild(addToCartButton)
				priceBox.appendChild(priceAndShipping)
				priceBox.appendChild(addToCartForm)
				productInfo.appendChild(priceBox)

				addToCartForm.appendChild(productQuantityDiv)
				// Dodanie wszystkich elementów do głównego kontenera produktu
				productDiv.appendChild(productInfo)
				slide.appendChild(productDiv)

				// Dodanie slajdu do Swipera
				swiper.appendSlide(slide)
			})
		}
		swiper.on('reachEnd', function (e) {
			const categoryId = e.params.el.getAttribute('data-swiper-id')
			const slideToIndex = e.previousIndex + 1
			loadMoreProducts(categoryId, slideToIndex)
		})
	})
})
