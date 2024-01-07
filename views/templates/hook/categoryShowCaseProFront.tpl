<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<div id="category-showcase-pro" class="product-sections">
	{foreach from=$products item=product}
		<div class="product-section cat-{$products|count} ">
			<div id="category-showcase-pro-header">
				<h2>{$product.category->name[$language['id']]|default:'No name'}</h2>
				<div class="box-control">
					<div class="swiper-navigation" data-swiper-id="{$product.category->id}">
						<div class="swiper-button-prev"></div>
						<div class="swiper-button-next"></div>
					</div>
				</div>
			</div>
			<div class="products">
				<div class="swiper-container" data-swiper-id="{$product.category->id}">
					<div class="swiper-wrapper">
						{foreach from=$product.products item=product}

							<div class="swiper-slide">
								<div class="product ">
									{if $product.cover}
										<a href="{$product.url}" class="thumbnail product-thumbnail">
											<picture>
												{if !empty($product.cover.bySize.home_default.sources.avif)}
													<source srcset="{$product.cover.bySize.home_default.sources.avif}" type="image/avif"
														loading="lazy">
												{/if}
												{if !empty($product.cover.bySize.home_default.sources.webp)}
													<source srcset="{$product.cover.bySize.home_default.sources.webp}" type="image/webp"
														loading="lazy">
												{/if}
												<img src="{$product.cover.bySize.home_default.url}"
													alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
													loading="lazy" data-full-size-image-url="{$product.cover.large.url}"
													width="{$product.cover.bySize.home_default.width}"
													height="{$product.cover.bySize.home_default.height}" loading="lazy" />
											</picture>
										</a>
									{else}
										<a href="{$product.url}" class="thumbnail product-thumbnail">
											<picture>
												{if !empty($urls.no_picture_image.bySize.home_default.sources.avif)}
													<source srcset="{$urls.no_picture_image.bySize.home_default.sources.avif}"
														type="image/avif" loading="lazy">
												{/if}
												{if !empty($urls.no_picture_image.bySize.home_default.sources.webp)}
													<source srcset="{$urls.no_picture_image.bySize.home_default.sources.webp}"
														type="image/webp" loading="lazy">
												{/if}
												<img src="{$urls.no_picture_image.bySize.home_default.url}" loading="lazy"
													width="{$urls.no_picture_image.bySize.home_default.width}"
													height="{$urls.no_picture_image.bySize.home_default.height}" />
											</picture>
										</a>
									{/if}

									<div class="product-info">
										<span class="product-name"><a href="{$product.url}"
												content="{$product.url}">{$product.name|truncate:30:'...'}</a></span>
										<div class="price-box">
											{block name='product_price_and_shipping'}
												{if $product.show_price}
													<div class="product-price-and-shipping">
														{if $product.has_discount}
															{hook h='displayProductPriceBlock' product=$product type="old_price"}
															<span class="product-price" aria-label=" {l s='Price' d='categoryshowcasepro'}">
																{capture name='custom_price'}{hook h='displayProductPriceBlock' product=$product type='custom_price' hook_origin='products_list'}{/capture}
																{if '' !== $smarty.capture.custom_price}
																	{$smarty.capture.custom_price nofilter}
																{else}
																	{$product.price}
																{/if}
															</span>
															{if $product.discount_type === 'percentage'}
																<span
																	class="discount-percentage discount-product">{$product.discount_percentage}</span>
															{elseif $product.discount_type === 'amount'}
																<span
																	class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
															{/if}
														{else}
															<span class="regular-price product-price"
																aria-label="{l s='Regular price' d='categoryshowcasepro'}">{$product.regular_price}
															</span>
														{/if}
														{hook h='displayProductPriceBlock' product=$product type="before_price"}

														{hook h='displayProductPriceBlock' product=$product type='unit_price'}

														{hook h='displayProductPriceBlock' product=$product type='weight'}
													</div>
												{/if}
											{/block}
											<form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
												<input type="hidden" name="token" value="{$static_token}">
												<input type="hidden" name="id_product" value="{$product.id}"
													id="product_page_product_id">
												<input type="hidden" name="id_customization"
													value="{if $product.id_customization} {$product.id_customization} {else}0{/if}"
													id="product_customization_id" class="js-product-customization-id">

												<div class="product-quantity">
													<input type="number" name="qty" value="1" min="1" />
													<button class="add-to-cart " data-button-action="add-to-cart" type="submit">
														<i class="material-icons shopping-cart"></i>
													</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						{/foreach}
					</div>
				</div>
			</div>
			<a href="{$product.category->category_link}"
				class="more-in-category">{l s='more in category' d='categoryshowcasepro'}</a>
		</div>
	{/foreach}
</div>

<script>
	const static_token = '{$static_token}';
	const addToCart = '{$urls.pages.cart}';
</script>