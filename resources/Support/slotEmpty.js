import {Comment, Text} from "vue";

export default function slotEmpty(slot, slotProps = {}) {
    if (!slot) return false;
    return slot(slotProps).some((vnode) => {
        if (vnode.type === Comment) return false;

        if (Array.isArray(vnode.children) && !vnode.children.length) return false;

        return (
            vnode.type !== Text
            || (typeof vnode.children === 'string' && vnode.children.trim() !== '')
        );
    });
}
