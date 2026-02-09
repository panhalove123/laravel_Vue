<script setup>
import Table from '../components/table/Table.vue';
import THead from '../components/table/THead.vue';
import TBody from '../components/table/TBody.vue';
import Tr from '../components/table/Tr.vue';
import Th from '../components/table/Th.vue';
import Td from '../components/table/Td.vue';
import CreateButton from '../components/ui/CreateButton.vue';
import EditButton from '../components/ui/EditButton.vue';
import DeleteButton from '../components/ui/DeleteButton.vue';
import AuthorizationFallback from '../components/page/AuthorizationFallback.vue';
import ProductSlider from '../components/page/ProductSlider.vue';

import useProductStore from '../store/useProductStore';
import useSlider from '../composables/useSlider';
import useModalToast from '../composables/useModalToast';
import useHttpRequest from '../composables/useHttpRequest';

const productStore = useProductStore();

if (!productStore.products?.length) await productStore.loadProducts();

const { slider, sliderData, showSlider, hideSlider } = useSlider('product-crud');
const { showConfirmModal, showToast } = useModalToast();
const { destroy: deleteProduct, deleting } = useHttpRequest('/products');

const onDelete = (product) => {
    if (deleting.value) return;

    showConfirmModal(null, async (confirmed) => {
        if (!confirmed) return;

        const isDeleted = await deleteProduct(product?.id);
        if (isDeleted) {
            showToast(`"${product?.name}" deleted successfully...`);
            productStore.loadProducts();
        }
    });
};
</script>

<template>
    <AuthorizationFallback :permissions="['products-all', 'products-view']">
        <div class="w-full space-y-4 py-6">
            <div class="flex-between">
                <h2 class="text-active font-bold text-2xl">Products</h2>

                <CreateButton @click="showSlider(true)" />
            </div>

            <div class="w-full">
                <Table>
                    <THead>
                        <Tr>
                            <Th> Id </Th>
                            <Th> Name </Th>
                            <Th> Description </Th>
                            <Th> Price </Th>
                            <Th> Image </Th>
                            <Th> Action </Th>
                        </Tr>
                    </THead>

                    <TBody>
                        <Tr
                            v-for="product in productStore.products"
                            :key="product.id"
                        >
                            <Td>{{ product?.id }}</Td>
                            <Td>
                                <div class="text-emerald-500 dark:text-emerald-200">
                                    {{ product?.name }}
                                </div>
                            </Td>
                            <Td>
                                <div class="text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                    {{ product?.description }}
                                </div>
                            </Td>
                            <Td>
                                <div class="font-semibold">
                                    ${{ parseFloat(product?.price).toFixed(2) }}
                                </div>
                            </Td>
                            <Td>
                                <img
                                    v-if="product?.image"
                                    :src="product?.image"
                                    :alt="product?.name"
                                    class="h-10 w-10 object-cover rounded"
                                />
                                <span v-else class="text-gray-400 text-sm">No image</span>
                            </Td>
                            <Td class="align-middle">
                                <div class="flex flex-col gap-2">
                                    <EditButton
                                        @click="showSlider(true, product)"
                                    />
                                    <DeleteButton @click="onDelete(product)" />
                                </div>
                            </Td>
                        </Tr>
                    </TBody>
                </Table>
            </div>
        </div>

        <ProductSlider
            :show="slider"
            :product="sliderData"
            @hide="hideSlider"
        />
    </AuthorizationFallback>
</template>
