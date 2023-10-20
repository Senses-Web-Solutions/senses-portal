<template>
    <transition name="CollapseTransition" @enter="enter" @leave="leave">
        <slot />
    </transition>
</template>
<script>
import velocity from 'velocity-animate';

// const animate = (node, show, transitionName, done) => {
//     let ok;
//
//     function complete () {
//         if(!ok) {
//             ok = true;
//             done();
//         }
//
//         node.style.display = show ? 'block' : 'none';
//
//         velocity(node, transitionName, {
//             complete,
//             duration: 200,
//             easing: 'easeInOutQuad'
//         });
//
//         return {
//             stop () {
//                 velocity(node, 'finish');
//                 complete();
//             }
//         }
//     }
// }

export default {
    props: {
        displayValue: {
            type: String,
            default: 'block',
        },
    },

    methods: {
        enter(element, done) {
            element.style.display = this.displayValue;
            return velocity(element, 'slideDown', {
                complete: done,
                duration: 200,
                easing: 'ease-in-out',
            });
        },
        leave(element, done) {
            return velocity(element, 'slideUp', {
                complete: () => {
                    element.style.display = 'none';
                    done();
                },
                duration: 200,
                easing: 'easeInOutQuad',
            });
        },
    },
};
</script>
