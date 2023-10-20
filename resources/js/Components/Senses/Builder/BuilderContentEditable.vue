<template>
    <component :is="as" v-once :contenteditable="$attrs['is-editor']" @input="update" v-html="modelValue"></component>
</template>
<script>
    // https://jessieji.com/2022/contenteditable-vue
  export default {
    props: {
      modelValue: {
        type: String,
        default: '',
      },
      as: {
        type:String,
        default:'div'
      }
    },
    emits: ['update:modelValue'],
    watch: {
      modelValue(v) {
      // or use a debounce with certain timeout here to check if user stops typing
        if (document.activeElement === this.$el) {
          return;
        }

        this.$el.innerText = v;
      },
    },
    methods: {
      update(e) {
        this.$emit('update:modelValue', e.target.innerText.replace(/\n/g, '<br>'))
      },
    },
  }
</script>