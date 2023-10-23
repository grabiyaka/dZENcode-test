<template>
<div>
    <div ref="quillEditor"></div>
</div>
</template>

<script>
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

export default {
    props: {
        value: String,
    },
    mounted() {
        this.quill = new Quill(this.$refs.quillEditor, {
            theme: 'snow',
            placeholder: this.generatePlaceholder()
        });

        this.quill.on('text-change', () => {
            this.$emit('input', this.quill.root.innerHTML);
        });
    },
    methods: {
        generatePlaceholder() {
            const placeholders = [
                "Your idea here...",
                "What's on your mind?",
                "Write down your thoughts...",
                "Your text here...",
                "Share your opinion...",
                "Start typing...",
                "Get inspired and write...",
                "Share your story...",
                "Record your idea...",
                "Interesting text here...",
            ];
            const randomIndex = Math.floor(Math.random() * placeholders.length);
            return placeholders[randomIndex];
        }
    },
    watch: {
        value(newVal) {
            this.quill.root.innerHTML = newVal;
        }
    },
};
</script>
