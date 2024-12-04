<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Textarea } from "@/components/ui/textarea";
import { Checkbox } from "@/components/ui/checkbox";
import {
	Select,
	SelectContent,
	SelectItem,
	SelectTrigger,
	SelectValue,
} from "@/components/ui/select";
import {
	Dialog,
	DialogContent,
	DialogHeader,
	DialogTitle,
	DialogDescription,
	DialogFooter,
} from "@/components/ui/dialog";
import { ref, watch, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "@/components/ui/toast/use-toast";
import { Toaster } from "@/components/ui/toast";

defineOptions({ layout: AdminLayout });

const props = defineProps({
	products: Object,
	categories: Array,
	filters: Object,
});

const search = ref(props.filters.search || "");
const categoryFilter = ref(props.filters.category || "null");

const form = useForm({
	product_name: "",
	description: "",
	price: "",
	categories: [],
	image: null,
	quantity: 1,
	expiration_date: "",
});

const inventoryForm = useForm({
	quantity: 1,
	expiration_date: "",
});

const showAddDialog = ref(false);
const showInventoryDialog = ref(false);
const selectedProduct = ref(null);
const showDeleteDialog = ref(false);
const productToDelete = ref(null);

function debounce(fn, delay) {
	let timeoutId;
	return (...args) => {
		clearTimeout(timeoutId);
		timeoutId = setTimeout(() => fn(...args), delay);
	};
}

const updateFilters = debounce(() => {
	router.get(
		route("admin.products.index"),
		{ search: search.value || null, category: categoryFilter.value || null },
		{ preserveState: true, preserveScroll: true, replace: true }
	);
}, 300);

watch([search, categoryFilter], () => {
	updateFilters();
});

const deleteProduct = () => {
	if (!productToDelete.value) return;

	router.delete(route("admin.products.destroy", productToDelete.value.id), {
		preserveScroll: true,
		preserveState: true,
		onSuccess: () => {
			showDeleteDialog.value = false;
			productToDelete.value = null;
		},
	});
};

const { toast } = useToast();

const submitProduct = () => {
	// Add validation checks
	if (
		!form.product_name ||
		!form.description ||
		!form.price ||
		!form.image ||
		!form.expiration_date
	) {
		toast({
			title: "Error",
			description: "Please fill all required fields",
			variant: "destructive",
		});
		return;
	}

	if (!form.categories.length) {
		toast({
			title: "Error",
			description: "Please select at least one category",
			variant: "destructive",
		});
		return;
	}

	form.post(route("admin.products.store"), {
		forceFormData: true,
		preserveScroll: true,
		onSuccess: () => {
			toast({
				title: "Success",
				description: "Product created successfully",
			});
			showAddDialog.value = false;
			form.reset();
		},
		onError: (errors) => {
			toast({
				title: "Error",
				description: Object.values(errors)[0],
				variant: "destructive",
			});
		},
	});
};

const updateInventory = () => {
	if (!selectedProduct.value) return;

	inventoryForm.post(route("admin.products.inventory.update", selectedProduct.value.id), {
		onSuccess: () => {
			showInventoryDialog.value = false;
			inventoryForm.reset();
			selectedProduct.value = null;
		},
	});
};

const formatID = (id) => String(id); // Convert numbers to strings

const toggleCategory = (categoryId) => {
	const id = String(categoryId);
	const index = form.categories.indexOf(id);
	if (index === -1) {
		form.categories.push(id);
	} else {
		form.categories.splice(index, 1);
	}
};

// Add form error handling
const hasErrors = computed(() => Object.keys(form.errors).length > 0);
</script>

<template>
	<Head title="| Products" />

	<div class="space-y-6">
		<!-- Header -->
		<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
			<h2 class="text-2xl font-semibold">Products</h2>

			<!-- Actions -->
			<div class="flex flex-col gap-4 sm:flex-row">
				<Input
					type="search"
					placeholder="Search products..."
					class="w-full sm:w-[250px]"
					v-model="search"
				/>

				<Select v-model="categoryFilter">
					<SelectTrigger class="w-full sm:w-[180px]">
						<SelectValue placeholder="All Categories" />
					</SelectTrigger>
					<SelectContent>
						<SelectItem value="null">All Categories</SelectItem>
						<SelectItem
							v-for="category in categories"
							:key="category.category_id"
							:value="category.category_id.toString()"
						>
							{{ category.category_name }}
						</SelectItem>
					</SelectContent>
				</Select>

				<Button @click="showAddDialog = true">Add Product</Button>
			</div>
		</div>

		<!-- Add Product Dialog -->
		<Dialog v-model:open="showAddDialog">
			<DialogContent class="sm:max-w-[500px]">
				<DialogHeader>
					<DialogTitle>Add New Product</DialogTitle>
				</DialogHeader>
				<form @submit.prevent="submitProduct" class="space-y-4">
					<div class="space-y-2">
						<label>Product Name</label>
						<Input v-model="form.product_name" />
					</div>
					<div class="space-y-2">
						<label>Description</label>
						<Textarea v-model="form.description" />
					</div>
					<div class="space-y-2">
						<label>Price</label>
						<Input type="number" step="0.01" v-model="form.price" />
					</div>
					<div class="space-y-2">
						<label class="font-medium">Categories</label>
						<p class="text-muted-foreground text-sm mb-3">
							Select at least one category for your product.
						</p>
						<div class="grid sm:grid-cols-2 gap-4">
							<div
								v-for="category in categories"
								:key="category.category_id"
								class="flex items-center space-x-2"
							>
								<Checkbox
									:id="`category-${category.category_id}`"
									:checked="form.categories.includes(formatID(category.category_id))"
									@update:checked="() => toggleCategory(category.category_id)"
								/>
								<label
									:for="`category-${category.category_id}`"
									class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
								>
									{{ category.category_name }}
								</label>
							</div>
						</div>
					</div>
					<div class="space-y-2">
						<label>Image</label>
						<Input
							type="file"
							accept="image/*"
							@input="(e) => (form.image = e.target.files[0])"
						/>
					</div>
					<div class="space-y-2">
						<label>Initial Stock Quantity</label>
						<Input type="number" v-model="form.quantity" min="1" />
					</div>
					<div class="space-y-2">
						<label>Expiration Date</label>
						<Input type="date" v-model="form.expiration_date" />
					</div>
					<Button type="submit" :disabled="form.processing || hasErrors" class="w-full">
						{{ form.processing ? "Creating Product..." : "Create Product" }}
					</Button>
				</form>
			</DialogContent>
		</Dialog>

		<!-- Products Grid -->
		<div class="sm:grid-cols-2 lg:grid-cols-3 grid gap-6">
			<Card v-for="product in products.data" :key="product.id">
				<CardHeader class="p-0 border-b">
					<img
						:src="product.image"
						:alt="product.name"
						class="aspect-[4/3] w-full object-cover rounded-t-lg"
					/>
				</CardHeader>
				<CardContent class="p-6 space-y-4">
					<div>
						<CardTitle class="text-base">{{ product.name }}</CardTitle>
						<p class="text-muted-foreground mt-1 text-sm">
							₱{{ product.price.toLocaleString() }}
						</p>
					</div>

					<div class="flex flex-wrap gap-2">
						<span
							v-for="category in product.categories"
							:key="category.id"
							class="bg-secondary text-secondary-foreground px-2.5 py-0.5 rounded-full text-xs"
						>
							{{ category.name }}
						</span>
					</div>

					<div class="flex items-center justify-between text-sm">
						<span>Stock: {{ product.stock }}</span>
						<span
							:class="{
								'text-emerald-600': product.status === 'In Stock',
								'text-red-600': product.status === 'Out of Stock',
							}"
						>
							{{ product.status }}
						</span>
					</div>

					<div class="flex gap-2 pt-4">
						<Button
							variant="outline"
							size="sm"
							@click="
								selectedProduct = product;
								showInventoryDialog = true;
							"
						>
							Update Stock
						</Button>
						<Button
							variant="destructive"
							size="sm"
							@click="
								productToDelete = product;
								showDeleteDialog = true;
							"
						>
							Delete
						</Button>
					</div>
				</CardContent>
			</Card>
		</div>

		<!-- Inventory Dialog -->
		<Dialog v-model:open="showInventoryDialog">
			<DialogContent>
				<DialogHeader>
					<DialogTitle>Update Inventory</DialogTitle>
				</DialogHeader>
				<form @submit.prevent="updateInventory" class="space-y-4">
					<div class="space-y-2">
						<label>Product</label>
						<p class="text-muted-foreground text-sm">
							{{ selectedProduct?.name }}
						</p>
					</div>

					<div class="space-y-2">
						<label>Current Stock</label>
						<p class="text-muted-foreground text-sm">
							{{ selectedProduct?.stock }} units
						</p>
					</div>

					<div class="space-y-2">
						<label>Add Quantity</label>
						<Input
							type="number"
							v-model="inventoryForm.quantity"
							min="1"
							placeholder="Enter quantity to add"
						/>
					</div>

					<div class="space-y-2">
						<label>Expiration Date</label>
						<Input
							type="date"
							v-model="inventoryForm.expiration_date"
							:min="new Date().toISOString().split('T')[0]"
						/>
					</div>

					<DialogFooter>
						<Button type="submit" :disabled="inventoryForm.processing">
							{{ inventoryForm.processing ? "Updating..." : "Update Stock" }}
						</Button>
					</DialogFooter>
				</form>
			</DialogContent>
		</Dialog>

		<!-- Delete Confirmation Dialog -->
		<Dialog v-model:open="showDeleteDialog">
			<DialogContent>
				<DialogHeader>
					<DialogTitle>Delete Product</DialogTitle>
					<DialogDescription>
						Are you sure you want to delete "{{ productToDelete?.name }}"? This action
						cannot be undone.
					</DialogDescription>
				</DialogHeader>
				<DialogFooter class="flex gap-2 justify-end">
					<Button variant="outline" @click="showDeleteDialog = false"> Cancel </Button>
					<Button
						variant="destructive"
						@click="deleteProduct"
						:disabled="router.processing"
					>
						{{ router.processing ? "Deleting..." : "Delete" }}
					</Button>
				</DialogFooter>
			</DialogContent>
		</Dialog>
	</div>

	<Toaster />
</template>
