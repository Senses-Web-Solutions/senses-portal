<template>
    <Block :class="{ 'w-full': content.width === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'reduced', 'mb-0': content.bottom_space === 'none', 'mb-10': content.bottom_space === 'small', 'mb-16': content.bottom_space === 'medium', 'mb-24': content.bottom_space === 'large' }">
        <div class="grid grid-cols-2 gap-8 my-12">
            <TravelInformation
                :block-key="blockKey"
                :content="content"
                @update:content="emit('update:content', $event)"
                @add="addTravelInformation"
                @destroy="destroyTravelInformation"
            ></TravelInformation>
            <AdditionalInformation
                :block-key="blockKey"
                :content="content"
                @update:content="emit('update:content', $event)"
                @add="addAdditionalInformation"
                @destroy="destroyAdditionalInformation"
            ></AdditionalInformation>
        </div>
    </Block>
</template>
<script>
import { defineComponent } from "vue";
import { Block, BlockMixin, defineBlock } from "@senses/builder";
import AdditionalInformation from "../../../components/blocks/partials/QuoteTwoColumn/AdditionalInformation.vue";
import TravelInformation from "../../../components/blocks/partials/QuoteTwoColumn/TravelInformation.vue";
import Icon from "../../icons/svg/layouts-two-equal-cols.svg?raw";
// import QuoteTwoColumnIcon from "../../icons/svg/quote-two-column.svg?raw";

export default defineComponent({
    components: { Block, AdditionalInformation, TravelInformation },
    mixins: [BlockMixin],
    block: defineBlock({
        name: "contents.quote-two-column",
        displayName: "Quote Two Column",
        categories: ["contents"],
        icon: Icon,
        defaultContent: {
            justify_text: 'left',
            bottom_space: 'small',
            width: 'standard',
            travelInformation: [
                {
                    title: "",
                    text: "",
                    date: "Sep 20",
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>',
                },
            ],
            additionalInformation: [
                {
                    title: "",
                    text: "",
                },
            ],
        },
    }),
    setup(props, { emit }) {
        const addTravelInformation = (
            index = props.content.travelInformation.length - 1
        ) => {
            const content = props.content;
            content.travelInformation.splice(index + 1, 0, {
                title: "",
                text: "",
                date: "Sep 20",
                icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>',
            });
            emit("update:content", content);
        };

        const addAdditionalInformation = (
            index = props.content.additionalInformation.length - 1
        ) => {
            const content = props.content;
            content.additionalInformation.splice(index + 1, 0, {
                title: "",
                text: "",
            });
            emit("update:content", content);
        };

        const destroyTravelInformation = (
            index = props.content.travelInformation.length - 1
        ) => {
            const content = props.content;
            content.travelInformation.splice(index, 1);
            emit("update:content", content);
        };

        const destroyAdditionalInformation = (
            index = props.content.additionalInformation.length - 1
        ) => {
            const content = props.content;
            content.additionalInformation.splice(index, 1);
            emit("update:content", content);
        };

        return {
            emit,
            addTravelInformation,
            addAdditionalInformation,
            destroyTravelInformation,
            destroyAdditionalInformation,
        };
    },
});
</script>
