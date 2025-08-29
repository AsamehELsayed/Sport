<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AdminLayout from '@/layouts/AdminLayout.vue';
import SettingsLayout from '@/layouts/admin/SettingsLayout.vue';

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
    timezones: {
        type: Object,
        required: true,
    },
    status: {
        type: String,
        default: '',
    },
});

const breadcrumbItems = [
    {
        title: 'Website Settings',
        href: '/admin/settings/account',
    },
];

// useForm for local state management
const form = useForm({
    primary_color: props.settings.primary_color || '#3B82F6',
    secondary_color: props.settings.secondary_color || '#1F2937',
    accent_color: props.settings.accent_color || '#10B981',
    website_name: props.settings.website_name || 'Website Name',
    page_name: props.settings.page_name || 'Page Name',
    timezone: props.settings.timezone || 'UTC',
    logo_path: props.settings.logo_path ? `/storage/${props.settings.logo_path}` : null,
    logo: null,
    // Hero section
    hero_background_image: props.settings.hero_background_image || null,
    hero_background_image_file: null,
    hero_badge_text: props.settings.hero_badge_text || '🔥 New Collection Available',
    hero_title_line1: props.settings.hero_title_line1 || 'Unleash Your',
    hero_title_line2: props.settings.hero_title_line2 || 'Athletic Power',
    hero_subtitle: props.settings.hero_subtitle || 'Discover premium sports equipment designed for champions. From professional-grade gear to everyday athletic essentials, elevate your performance with our cutting-edge collection.',
    hero_cta_primary_text: props.settings.hero_cta_primary_text || 'Shop Collection',
    hero_cta_primary_url: props.settings.hero_cta_primary_url || '/categories',
    hero_cta_secondary_text: props.settings.hero_cta_secondary_text || 'Watch Stories',
    hero_cta_secondary_url: props.settings.hero_cta_secondary_url || null,
    _method: 'patch',
});

const logoPreview = ref(props.settings.logo_path);
const heroImagePreview = ref(props.settings.hero_background_image);

const logoInput = ref<HTMLInputElement | null>(null);
const heroImageInput = ref<HTMLInputElement | null>(null);

// Handle logo file input
const handleLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

// Handle hero background image file input
const handleHeroImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.hero_background_image_file = file;
        heroImagePreview.value = URL.createObjectURL(file);
    }
};

// Handle drag & drop for logo
const handleLogoDrop = (event: DragEvent) => {
    const file = event.dataTransfer?.files[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

// Handle drag & drop for hero image
const handleHeroImageDrop = (event: DragEvent) => {
    const file = event.dataTransfer?.files[0];
    if (file) {
        form.hero_background_image_file = file;
        heroImagePreview.value = URL.createObjectURL(file);
    }
};

// Preset color schemes
const colorPresets = [
    { name: 'Blue', primary: '#3B82F6', secondary: '#1F2937', accent: '#10B981' },
    { name: 'Purple', primary: '#8B5CF6', secondary: '#1F2937', accent: '#EC4899' },
    { name: 'Green', primary: '#10B981', secondary: '#1F2937', accent: '#F59E0B' },
    { name: 'Red', primary: '#EF4444', secondary: '#1F2937', accent: '#F59E0B' },
    { name: 'Orange', primary: '#F97316', secondary: '#1F2937', accent: '#10B981' },
    { name: 'Pink', primary: '#EC4899', secondary: '#1F2937', accent: '#8B5CF6' },
];

const applyColorPreset = (preset) => {
    form.primary_color = preset.primary;
    form.secondary_color = preset.secondary;
    form.accent_color = preset.accent;
};

const handleSubmit = () => {
    form.post(route('admin.settings.account.update'));
};
</script>

<template>
    <AdminLayout>
        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Website Settings" description="Manage your website information, appearance, and hero section" />

                <!-- Status Message -->
                <div v-if="status" class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-800">{{ status }}</p>
                </div>

                <form class="space-y-6" @submit.prevent="handleSubmit">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium">Basic Information</h3>

                        <div class="grid gap-2">
                            <Label for="website_name">Website Name</Label>
                            <Input
                                id="website_name"
                                class="mt-1 block w-full"
                                name="website_name"
                                v-model="form.website_name"
                                placeholder="Your website name"
                            />
                            <InputError :message="form.errors.website_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="page_name">Page Name</Label>
                            <Input
                                id="page_name"
                                class="mt-1 block w-full"
                                name="page_name"
                                v-model="form.page_name"
                                placeholder="Your page name"
                            />
                            <InputError :message="form.errors.page_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="timezone">Timezone</Label>
                            <div class="relative">
                                <select
                                    id="timezone"
                                    name="timezone"
                                    class="mt-1 block w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    v-model="form.timezone"
                                >
                                    <option value="">Select your timezone</option>
                                    <optgroup label="Popular Timezones">
                                        <option value="UTC">UTC</option>
                                        <option value="America/New_York">Eastern Time (US & Canada)</option>
                                        <option value="America/Chicago">Central Time (US & Canada)</option>
                                        <option value="America/Los_Angeles">Pacific Time (US & Canada)</option>
                                        <option value="Europe/London">London</option>
                                        <option value="Europe/Paris">Paris</option>
                                        <option value="Asia/Tokyo">Tokyo</option>
                                    </optgroup>
                                    <optgroup label="All Timezones">
                                        <option v-for="(label, value) in timezones" :key="value" :value="value">
                                            {{ label }}
                                        </option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>

                        <!-- Logo with Drag & Drop -->
                        <div class="grid gap-2">
                            <Label for="logo">Logo</Label>
                            <div class="flex items-center gap-4">
                                <div v-if="logoPreview" class="w-16 h-16 border rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
                                    <img :src="logoPreview" alt="Logo preview" class="w-full h-full object-cover" />
                                </div>
                                <div v-else class="w-16 h-16 border rounded-lg bg-gray-50 flex items-center justify-center">
                                    <span class="text-gray-400 text-xs">No logo</span>
                                </div>

                                <div class="flex-1">
                                    <!-- Drag & Drop Zone -->
                                    <div
                                        class="border-2 border-dashed rounded-lg p-4 text-center cursor-pointer hover:border-primary transition-colors"
                                        @click="logoInput?.click()"
                                        @dragover.prevent
                                        @drop.prevent="handleLogoDrop"
                                    >
                                        <p class="text-sm text-gray-600">
                                            Drag & drop logo here, or
                                            <span class="text-primary font-medium">browse</span>
                                        </p>
                                        <p class="text-xs text-gray-500">Supports JPG, PNG, GIF, SVG (max 2MB)</p>
                                    </div>

                                    <!-- Hidden File Input -->
                                    <input
                                        id="logo"
                                        ref="logoInput"
                                        type="file"
                                        class="hidden"
                                        name="logo"
                                        accept="image/*"
                                        @change="handleLogoChange"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Section -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium">Hero Section</h3>

                        <!-- Hero Background Image -->
                        <div class="grid gap-2">
                            <Label for="hero_background_image">Hero Background Image</Label>
                            <div class="flex items-center gap-4">
                                <div v-if="heroImagePreview" class="w-32 h-20 border rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
                                    <img :src="heroImagePreview" alt="Hero image preview" class="w-full h-full object-cover" />
                                </div>
                                <div v-else class="w-32 h-20 border rounded-lg bg-gray-50 flex items-center justify-center">
                                    <span class="text-gray-400 text-xs">No image</span>
                                </div>

                                <div class="flex-1">
                                    <!-- Drag & Drop Zone -->
                                    <div
                                        class="border-2 border-dashed rounded-lg p-4 text-center cursor-pointer hover:border-primary transition-colors"
                                        @click="heroImageInput?.click()"
                                        @dragover.prevent
                                        @drop.prevent="handleHeroImageDrop"
                                    >
                                        <p class="text-sm text-gray-600">
                                            Drag & drop hero image here, or
                                            <span class="text-primary font-medium">browse</span>
                                        </p>
                                        <p class="text-xs text-gray-500">Supports JPG, PNG, GIF, SVG (max 5MB)</p>
                                    </div>

                                    <!-- Hidden File Input -->
                                    <input
                                        id="hero_background_image"
                                        ref="heroImageInput"
                                        type="file"
                                        class="hidden"
                                        name="hero_background_image"
                                        accept="image/*"
                                        @change="handleHeroImageChange"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Hero Badge -->
                        <div class="grid gap-2">
                            <Label for="hero_badge_text">Hero Badge Text</Label>
                            <Input
                                id="hero_badge_text"
                                class="mt-1 block w-full"
                                name="hero_badge_text"
                                v-model="form.hero_badge_text"
                                placeholder="🔥 New Collection Available"
                            />
                        </div>

                        <!-- Hero Title -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="hero_title_line1">Hero Title Line 1</Label>
                                <Input
                                    id="hero_title_line1"
                                    class="mt-1 block w-full"
                                    name="hero_title_line1"
                                    v-model="form.hero_title_line1"
                                    placeholder="Unleash Your"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label for="hero_title_line2">Hero Title Line 2</Label>
                                <Input
                                    id="hero_title_line2"
                                    class="mt-1 block w-full"
                                    name="hero_title_line2"
                                    v-model="form.hero_title_line2"
                                    placeholder="Athletic Power"
                                />
                            </div>
                        </div>

                        <!-- Hero Subtitle -->
                        <div class="grid gap-2">
                            <Label for="hero_subtitle">Hero Subtitle</Label>
                            <textarea
                                id="hero_subtitle"
                                class="mt-1 block w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                name="hero_subtitle"
                                v-model="form.hero_subtitle"
                                rows="3"
                                placeholder="Discover premium sports equipment designed for champions..."
                            ></textarea>
                        </div>

                        <!-- Hero CTA Buttons -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-4">
                                <h4 class="text-sm font-medium">Primary CTA Button</h4>
                                <div class="grid gap-2">
                                    <Label for="hero_cta_primary_text">Button Text</Label>
                                    <Input
                                        id="hero_cta_primary_text"
                                        class="mt-1 block w-full"
                                        name="hero_cta_primary_text"
                                        v-model="form.hero_cta_primary_text"
                                        placeholder="Shop Collection"
                                    />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="hero_cta_primary_url">Button URL</Label>
                                    <Input
                                        id="hero_cta_primary_url"
                                        class="mt-1 block w-full"
                                        name="hero_cta_primary_url"
                                        v-model="form.hero_cta_primary_url"
                                        placeholder="/categories"
                                    />
                                </div>
                            </div>
                            <div class="space-y-4">
                                <h4 class="text-sm font-medium">Secondary CTA Button</h4>
                                <div class="grid gap-2">
                                    <Label for="hero_cta_secondary_text">Button Text</Label>
                                    <Input
                                        id="hero_cta_secondary_text"
                                        class="mt-1 block w-full"
                                        name="hero_cta_secondary_text"
                                        v-model="form.hero_cta_secondary_text"
                                        placeholder="Watch Stories"
                                    />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="hero_cta_secondary_url">Button URL</Label>
                                    <Input
                                        id="hero_cta_secondary_url"
                                        class="mt-1 block w-full"
                                        name="hero_cta_secondary_url"
                                        v-model="form.hero_cta_secondary_url"
                                        placeholder="/stories"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Color Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium">Color Settings</h3>

                        <!-- Color Presets -->
                        <div class="space-y-2">
                            <Label>Quick Color Presets</Label>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2">
                                <button
                                    v-for="preset in colorPresets"
                                    :key="preset.name"
                                    type="button"
                                    class="p-3 border rounded-lg hover:border-primary transition-colors"
                                    @click="applyColorPreset(preset)"
                                >
                                    <div class="flex gap-1 mb-2">
                                        <div class="w-4 h-4 rounded" :style="{ backgroundColor: preset.primary }"></div>
                                        <div class="w-4 h-4 rounded" :style="{ backgroundColor: preset.secondary }"></div>
                                        <div class="w-4 h-4 rounded" :style="{ backgroundColor: preset.accent }"></div>
                                    </div>
                                    <span class="text-xs font-medium">{{ preset.name }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="grid gap-2">
                                <Label for="primary_color">Primary Color</Label>
                                <div class="flex gap-2">
                                    <Input
                                        id="primary_color"
                                        type="color"
                                        class="mt-1 w-16 h-10 p-1 border border-input rounded-md cursor-pointer"
                                        name="primary_color"
                                        v-model="form.primary_color"
                                    />
                                    <Input
                                        type="text"
                                        class="mt-1 block flex-1"
                                        v-model="form.primary_color"
                                        placeholder="#3B82F6"
                                    />
                                </div>
                            </div>

                            <div class="grid gap-2">
                                <Label for="secondary_color">Secondary Color</Label>
                                <div class="flex gap-2">
                                    <Input
                                        id="secondary_color"
                                        type="color"
                                        class="mt-1 w-16 h-10 p-1 border border-input rounded-md cursor-pointer"
                                        name="secondary_color"
                                        v-model="form.secondary_color"
                                    />
                                    <Input
                                        type="text"
                                        class="mt-1 block flex-1"
                                        v-model="form.secondary_color"
                                        placeholder="#1F2937"
                                    />
                                </div>
                            </div>

                            <div class="grid gap-2">
                                <Label for="accent_color">Accent Color</Label>
                                <div class="flex gap-2">
                                    <Input
                                        id="accent_color"
                                        type="color"
                                        class="mt-1 w-16 h-10 p-1 border border-input rounded-md cursor-pointer"
                                        name="accent_color"
                                        v-model="form.accent_color"
                                    />
                                    <Input
                                        type="text"
                                        class="mt-1 block flex-1"
                                        v-model="form.accent_color"
                                        placeholder="#10B981"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Color Preview -->
                        <div class="mt-4 p-4 border border-input rounded-lg">
                            <h4 class="text-sm font-medium mb-3">Live Color Preview</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded border" :style="{ backgroundColor: form.primary_color }"></div>
                                        <span class="text-sm font-medium">Primary</span>
                                    </div>
                                    <div class="h-8 rounded" :style="{ backgroundColor: form.primary_color }"></div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded border" :style="{ backgroundColor: form.secondary_color }"></div>
                                        <span class="text-sm font-medium">Secondary</span>
                                    </div>
                                    <div class="h-8 rounded" :style="{ backgroundColor: form.secondary_color }"></div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded border" :style="{ backgroundColor: form.accent_color }"></div>
                                        <span class="text-sm font-medium">Accent</span>
                                    </div>
                                    <div class="h-8 rounded" :style="{ backgroundColor: form.accent_color }"></div>
                                </div>
                            </div>

                            <!-- Live Website Preview -->
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <h5 class="text-sm font-medium mb-3">Live Website Preview</h5>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="px-3 py-1 rounded text-white text-sm" :style="{ backgroundColor: form.primary_color }">
                                            Primary Button
                                        </div>
                                        <div class="px-3 py-1 rounded border text-sm" :style="{ backgroundColor: form.secondary_color, color: '#fff' }">
                                            Secondary Button
                                        </div>
                                        <div class="px-3 py-1 rounded text-white text-sm" :style="{ backgroundColor: form.accent_color }">
                                            Accent Button
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        These colors will be applied to your entire website
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button type="submit">Save Changes</Button>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AdminLayout>
</template>
