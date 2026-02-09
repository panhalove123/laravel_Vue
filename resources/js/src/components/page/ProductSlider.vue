<template>
    <Slider
        :show="show"
        :title="title"
        @hide="emit('hide')"
    >
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <FormInput
                v-model="formData.name"
                type="text"
                label="Product Name"
                placeholder="Enter product name"
                :error="formErrors.name?.[0]"
                required
            />

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Description
                </label>
                <textarea
                    v-model="formData.description"
                    placeholder="Enter product description"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="4"
                ></textarea>
                <FormLabelError v-if="formErrors.description?.[0]" :error="formErrors.description?.[0]" />
            </div>

            <FormInput
                v-model.number="formData.price"
                type="number"
                label="Price"
                placeholder="Enter product price"
                step="0.01"
                :error="formErrors.price?.[0]"
                required
            />

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Product Image
                </label>
                <input
                    type="file"
                    accept="image/*"
                    @change="handleImageChange"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700"
                />
                <FormLabelError v-if="formErrors.image?.[0]" :error="formErrors.image?.[0]" />
            </div>

            <div v-if="previewImage || product?.image" class="mt-2">
                <img
                    :src="previewImage || product?.image"
                    alt="Preview"
                    class="max-w-xs h-40 object-cover rounded"
                />
            </div>

            <div class="flex gap-2 justify-end pt-4">
                <button
                    type="button"
                    @click="emit('hide')"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="submitting"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50"
                >
                    {{ submitting ? 'Saving...' : product?.id ? 'Update' : 'Create' }}
                </button>
            </div>
        </form>
    </Slider>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import Slider from '../ui/Slider.vue';
import FormInput from '../ui/FormInput.vue';
import FormLabelError from '../ui/FormLabelError.vue';
import useProductStore from '../../store/useProductStore';
import useModalToast from '../../composables/useModalToast';
import useHttpRequest from '../../composables/useHttpRequest';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    product: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['hide']);

const productStore = useProductStore();
const { showToast } = useModalToast();
const {
    store: createProduct,
    saving: savingCreate,
    update: updateProduct,
    updating: savingUpdate,
} = useHttpRequest('/products');

const submitting = computed(() => savingCreate.value || savingUpdate.value);

const title = computed(() =>
    props.product?.id ? `Update "${props.product?.name}"` : 'Add new product',
);

const initialFormData = () => ({
    name: '',
    description: '',
    price: '',
});

const formData = ref(initialFormData());
const formErrors = ref({});
const previewImage = ref(null);
const imageFile = ref(null);

watch(
    () => props.show,
    () => {
        if (props.show) {
            if (props.product?.id) {
                formData.value = {
                    name: props.product.name,
                    description: props.product.description,
                    price: props.product.price,
                };
                previewImage.value = null;
            } else {
                formData.value = initialFormData();
                previewImage.value = null;
            }
            formErrors.value = {};
            imageFile.value = null;
        }
    }
);

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        imageFile.value = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const handleSubmit = async () => {
    formErrors.value = {};

    try {
        const data = {
            name: formData.value.name,
            description: formData.value.description,
            price: formData.value.price,
        };

        if (props.product?.id) {
            await updateProduct(data, props.product.id);
            showToast(`"${formData.value.name}" updated successfully...`);
            await productStore.loadProducts();
        } else {
            const formDataWithImage = new FormData();
            formDataWithImage.append('name', formData.value.name);
            formDataWithImage.append('description', formData.value.description);
            formDataWithImage.append('price', formData.value.price);
            if (imageFile.value) {
                formDataWithImage.append('image', imageFile.value);
            }

            await createProduct(formDataWithImage);
            showToast(`"${formData.value.name}" created successfully...`);
            await productStore.loadProducts();
        }
        emit('hide');
    } catch (error) {
        if (error.response?.data?.errors) {
            formErrors.value = error.response.data.errors;
        } else {
            showToast(error.message || 'An error occurred', 'error');
        }
    }
};
</script>
