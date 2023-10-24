<template>
<div>
    <div ref="quillEditor"></div>
    <div class="files-container">
        <input id="fileInput" type="file" @change="handleFileUpload" multiple>
        <label class="file-upload-button file-element" for="fileInput"></label>
        <div class="file-element" v-for="file in tempFiles" :key="file.name">
            <div>
                <img v-if="isImage(file) || isGif(file)" :src="getSrc(file)" alt="Image" />
                <pre v-else-if="isTextFile(file)">{{ file.content }}</pre>
                <span v-else>{{ file.name }}</span>
                <button class="cross-delete" @click="deleteFile(file)">&#10005</button>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

export default {
    props: {
        value: String,
    },
    data() {
        return {
            tempFiles: []
        }
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
        },
        handleFileUpload(event) {
            const selectedFiles = event.target.files;
            console.log(URL.createObjectURL(selectedFiles[0]));
            if (selectedFiles.length > 0) {
                this.tempFiles = this.tempFiles.concat(Array.from(selectedFiles));
            }
        },
        removeTempFile(file) {
            const index = this.tempFiles.indexOf(file);
            if (index > -1) {
                this.tempFiles.splice(index, 1);
            }
        },
        getSrc(file) {
            return URL.createObjectURL(file);
        },

        isImage(file) {
            // Список расширений изображений
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];

            // Получаем расширение файла (предполагаем, что расширение находится в конце имени файла)
            const fileExtension = file.name.split('.').pop().toLowerCase();

            // Проверяем, является ли расширение изображением
            return imageExtensions.includes(fileExtension);
        },

        isGif(file) {
            // Проверяем, является ли файл GIF-изображением
            return this.isImage(file) && file.name.toLowerCase().endsWith('.gif');
        },

        isTextFile(file) {
            // Список расширений текстовых файлов
            const textFileExtensions = ['txt'];

            // Получаем расширение файла
            const fileExtension = file.name.split('.').pop().toLowerCase();

            // Проверяем, является ли расширение текстовым файлом
            return textFileExtensions.includes(fileExtension);
        },
    },
    watch: {
        value(newVal) {
            this.quill.root.innerHTML = newVal;
        }
    },
};
</script>

<style lang="scss">
    
</style>
