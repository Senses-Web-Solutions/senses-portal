<script>
import { useSlots, watch, ref, nextTick, h } from 'vue';

export default {
    props: {
        flipKey: {
            type: [String, Boolean],
            default: ''
        },
        duration: {
            type: Number,
            default: 200
        }
    },
    setup(props, { expose }) {
        // To smoothly transition the height of the notification panel itself
        // we need the bounding rectangles for before and after the notification is removed.
        let beforeRect = null;
        let afterRect = null;
        const element = ref(null);
        const slots = useSlots();

        if (slots.default().length !== 1) {
            console.warn(
                'HeightReflow has been given more (or less) than 1 child element. This will break the component.'
            );
        }

        function trigger() {
            // record the rectangle before the dom is updated
            beforeRect = element.value.getBoundingClientRect();
            nextTick(() => {
                // record the rectangle after the dom is updated
                afterRect = element.value.getBoundingClientRect();
                
                // animate the height from before to after smoothly
                element.value.animate(
                    [
                        {
                            height: `${beforeRect.height}px`,
                        },
                        {
                            height: `${afterRect.height}px`,
                        },
                    ],
                    {
                        // same easing function and duration as tailwind's transition class
                        duration: props.duration,
                        easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                    }
                );
            });
        }

        watch(
            () => props.flipKey,
            trigger,
            { deep: true }
        );

        expose({
            trigger
        });

        return { element, trigger };
    },
    render() {
        return h(
            'div',
            {
                ref: 'element',
            },
            this.$slots.default()
        );
    },
};
</script>