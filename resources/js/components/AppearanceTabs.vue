<script setup>
import { ref } from 'vue';
import { Form } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

const props = defineProps({
    user: {
        type: Object,
        default: () => ({}),
    },
});

const primaryColor = ref(props.user.primary_color || '#3B82F6');
const secondaryColor = ref(props.user.secondary_color || '#1F2937');
const accentColor = ref(props.user.accent_color || '#10B981');
</script>

<template>
    <div class="space-y-6">
        <Tabs default-value="colors" class="w-full">
            <TabsList class="grid w-full grid-cols-2">
                <TabsTrigger value="colors">Colors</TabsTrigger>
                <TabsTrigger value="theme">Theme</TabsTrigger>
            </TabsList>

            <TabsContent value="colors" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <Label for="primary_color">Primary Color</Label>
                        <div class="flex gap-2">
                            <Input
                                id="primary_color"
                                type="color"
                                class="w-16 h-10 p-1 border border-input rounded-md"
                                v-model="primaryColor"
                            />
                            <Input
                                type="text"
                                class="flex-1"
                                :value="primaryColor"
                                placeholder="#3B82F6"
                                readonly
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="secondary_color">Secondary Color</Label>
                        <div class="flex gap-2">
                            <Input
                                id="secondary_color"
                                type="color"
                                class="w-16 h-10 p-1 border border-input rounded-md"
                                v-model="secondaryColor"
                            />
                            <Input
                                type="text"
                                class="flex-1"
                                :value="secondaryColor"
                                placeholder="#1F2937"
                                readonly
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="accent_color">Accent Color</Label>
                        <div class="flex gap-2">
                            <Input
                                id="accent_color"
                                type="color"
                                class="w-16 h-10 p-1 border border-input rounded-md"
                                v-model="accentColor"
                            />
                            <Input
                                type="text"
                                class="flex-1"
                                :value="accentColor"
                                placeholder="#10B981"
                                readonly
                            />
                        </div>
                    </div>
                </div>

                <!-- Color Preview -->
                <div class="p-4 border border-input rounded-lg">
                    <h4 class="text-sm font-medium mb-3">Color Preview</h4>
                    <div class="flex gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded border" :style="{ backgroundColor: primaryColor }"></div>
                            <span class="text-sm">Primary</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded border" :style="{ backgroundColor: secondaryColor }"></div>
                            <span class="text-sm">Secondary</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded border" :style="{ backgroundColor: accentColor }"></div>
                            <span class="text-sm">Accent</span>
                        </div>
                    </div>
                </div>
            </TabsContent>

            <TabsContent value="theme" class="space-y-4">
                <div class="space-y-2">
                    <Label>Theme</Label>
                    <div class="flex gap-2">
                        <Button variant="outline" class="flex-1">Light</Button>
                        <Button variant="outline" class="flex-1">Dark</Button>
                        <Button variant="outline" class="flex-1">System</Button>
                    </div>
                </div>
            </TabsContent>
        </Tabs>
    </div>
</template>
