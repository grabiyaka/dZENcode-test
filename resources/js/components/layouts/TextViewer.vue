<template>
<div class="fileControl">
    <div class="fileContent-container">
        <pre>{{ fileContent }}</pre>

    </div>
    <div class="control-panel">
        <button class="btn-save" v-if="!file" @click="downloadFile">
            <i class="bi bi-file-earmark-arrow-down"></i>
        </button>
        <button class="btn-close-modal" v-if="!file" @click="closeModal">
            &#10005
        </button>
    </div>

</div>
</template>

<script>
export default {
    props: {
        filePath: String,
        file: null
    },
    data() {
        return {
            fileContent: '',
        };
    },
    mounted() {
        if (this.file) {
            this.readFileContent();
        } else this.loadFileContent()
    },
    methods: {
        loadFileContent() {
            console.log(this.filePath);
            axios.get(this.filePath).then((response) => (this.fileContent = response.data, console.log(response.data)));

        },
        readFileContent() {
            const reader = new FileReader();
            reader.onload = (event) => {
                this.fileContent = event.target.result;
            };
            reader.readAsText(this.file); // Прочитываем содержимое файла
        },
        downloadFile() {

            const blob = new Blob([this.fileContent], {
                type: 'text/plain'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'textfile.txt';
            a.click();
            window.URL.revokeObjectURL(url);
        },
        closeModal(){
            this.$parent.txt.active = false
        }
    },
};
</script>

<style lang="scss">
.fileControl {

    position: relative;
    background: whitesmoke;
    padding: 5px;
    max-width: 100%;
    max-height: 80vh;

    .fileContent-container {
        position: relative;
        background: whitesmoke;
        padding: 5px;
        max-width: 100%;
        max-height: 80vh;
        overflow-y: scroll;

        pre {
            max-width: 100%;
        }
    }

    .control-panel {
        position: absolute;
        top: 10px;
        right: 10px;
        transition: all ease .3s;
        display: flex;

        // opacity: 0;
        // visibility: hidden;

        button {
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            margin: 10px;

            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            cursor: pointer;
        }

            i {
                font-size: 24px;
            }

            .btn-save {
                background-color: #007bff;
            }

            .btn-close-modal {
                background-color: #d9534f;
            }

    }
}
</style>
