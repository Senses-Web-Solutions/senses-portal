import { nextTick, onMounted } from 'vue';

export default function useHeightReflow(element) {
    function getElement() {
        if (element.value && element.value instanceof Node) {
            return element.value;
        }
        if (
            element.value &&
            element.value.el &&
            element.value.el instanceof Node
        ) {
            return element.value.el;
        }
        if (
            element.value &&
            element.value.$el &&
            element.value.$el instanceof Node
        ) {
            return element.value.$el;
        }
        return element;
    }

    let beforeRect = null;
    let afterRect = null;

    function doReflow() {
        try {
            getElement().animate(
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
                    duration: 200,
                    easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                }
            );
        } catch (e) {
            // beforeRect might be undefined
            // that's fine.
        }
    }

    function validate() {
        afterRect = getElement().getBoundingClientRect();

        if (!beforeRect || afterRect.height !== beforeRect.height) {
            doReflow();
        }
    }

    const observer = new MutationObserver(validate);

    onMounted(() => {
        nextTick(() => {
            beforeRect = getElement().getBoundingClientRect();
        });

        observer.observe(getElement(), {
            childList: true,
            subtree: false,
        });
    });
}
