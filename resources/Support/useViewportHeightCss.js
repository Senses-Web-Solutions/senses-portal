import { onMounted, ref } from "vue";

export default function useViewportHeightCss(additional = 112) {
    const heightCss = ref('');

    onMounted(() => {
        console.log('useviewportheight mounted');
        const headerHeight = document.querySelector(
            '[data-component="PageHeader"]'
        ).offsetHeight;
        heightCss.value = `calc(100vh - ${headerHeight + additional}px)`;
    });

    return heightCss;
}