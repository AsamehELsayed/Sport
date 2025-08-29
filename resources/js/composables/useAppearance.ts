import { usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

export function useAppearance() {
    const page = usePage();

        const appearance = computed(() => {
        // Check if page and props exist before accessing appearance
        if (!page || !page.props) {
            return {
                primary_color: '#3B82F6',
                secondary_color: '#1F2937',
                accent_color: '#10B981',
                page_name: 'SportApp',
                website_name: 'SportApp',
                logo_path: null,
            };
        }

        return page.props.appearance || {
            primary_color: '#3B82F6',
            secondary_color: '#1F2937',
            accent_color: '#10B981',
            page_name: 'SportApp',
            website_name: 'SportApp',
            logo_path: null,
        };
    });

        const applyAppearance = () => {
        // Only apply if we're in a browser environment
        if (typeof document === 'undefined') return;

        const root = document.documentElement;
        const { primary_color, secondary_color, accent_color, page_name, website_name, logo_path } = appearance.value;

        // Set CSS custom properties
        root.style.setProperty('--primary-color', primary_color);
        root.style.setProperty('--secondary-color', secondary_color);
        root.style.setProperty('--accent-color', accent_color);

        // Update page title
        if (website_name && website_name !== 'SportApp') {
            document.title = document.title.replace(/^[^-]*/, website_name);
        }

        // Add custom CSS classes with more comprehensive coverage
        const style = document.createElement('style');
        style.id = 'user-appearance-styles';
        style.textContent = `
            .bg-primary { background-color: ${primary_color} !important; }
            .text-primary { color: ${primary_color} !important; }
            .border-primary { border-color: ${primary_color} !important; }
            .hover\\:bg-primary:hover { background-color: ${primary_color} !important; }
            .hover\\:text-primary:hover { color: ${primary_color} !important; }
            .hover\\:border-primary:hover { border-color: ${primary_color} !important; }
            .focus\\:ring-primary:focus { --tw-ring-color: ${primary_color} !important; }

            .bg-secondary { background-color: ${secondary_color} !important; }
            .text-secondary { color: ${secondary_color} !important; }
            .border-secondary { border-color: ${secondary_color} !important; }
            .hover\\:bg-secondary:hover { background-color: ${secondary_color} !important; }
            .hover\\:text-secondary:hover { color: ${secondary_color} !important; }
            .hover\\:border-secondary:hover { border-color: ${secondary_color} !important; }

            .bg-accent { background-color: ${accent_color} !important; }
            .text-accent { color: ${accent_color} !important; }
            .border-accent { border-color: ${accent_color} !important; }
            .hover\\:bg-accent:hover { background-color: ${accent_color} !important; }
            .hover\\:text-accent:hover { color: ${accent_color} !important; }
            .hover\\:border-accent:hover { border-color: ${accent_color} !important; }

            /* Additional utility classes for better coverage */
            .ring-primary { --tw-ring-color: ${primary_color} !important; }
            .ring-secondary { --tw-ring-color: ${secondary_color} !important; }
            .ring-accent { --tw-ring-color: ${accent_color} !important; }
        `;

        // Remove existing styles and add new ones
        const existingStyle = document.getElementById('user-appearance-styles');
        if (existingStyle) {
            existingStyle.remove();
        }
        document.head.appendChild(style);
    };

    // Apply appearance when component mounts and when appearance changes
    watch(appearance, applyAppearance, { immediate: true, deep: true });

    return {
        appearance,
        applyAppearance,
    };
}
