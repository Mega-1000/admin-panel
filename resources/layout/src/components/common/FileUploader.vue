<template>
  <div class="modal-backdrop show c-fileUploader modal-scrollbar-measure" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <slot name="header"></slot>
          </h5>
          <button type="button" class="close" aria-label="Close">
            <span aria-hidden="true" @click="$emit('close')">&times;</span>
          </button>
        </div>
        <div v-if="importIsLoading" class="modal-body">
          <div class="loader">Loading...</div>
        </div>
        <div v-else class="modal-body">
          <div v-if="errors.length" class="row">
            <div class="col-md-12">
              <div class="alert alert-danger">{{ errors }}</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group"
                   :class="[{'has-error': kind.error===true},{'has-success': kind.error===false && kind.value !== ''}]">
                <label for="kind" class="col-md-5 col-form-label">Rodzaj importu</label>
                <div class="col-md-5">
                  <select v-model="kind.value" required id="kind" class="form-control">
                    <option value="" selected>-- wybierz --</option>
                    <option v-for="(kindLabel,kindKey) in kinds" :key="kindKey" :value="kindKey">
                      {{ kindLabel }}
                    </option>
                  </select>
                  <span class="form-control-feedback">{{ kind.errorMessage }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group file-group"
                   :class="[{'has-error': file.error===true},{'has-success': file.error===false && file.value !== ''}]">
                <label for="file" class="col-md-5 col-form-label">Plik Importu</label>
                <div class="col-md-7">
                  <div class="input-group-file">
                    <button class="btn btn-file">Dodaj plik<input id="file" ref="uploadFiles" @change="previewFiles"
                                                                  type="file" aria-describedby="file">
                    </button>
                    <span class="file-label">{{ file.name }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" :class="{'disabled':importIsLoading}" @click="importFile">
            Importuj
          </button>
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Zamknij</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'
import { ImportFileParams } from '@/types/TransactionsTypes'

@Component({
  components: {}
})
export default class FileUploader extends Vue {
  @Prop() private kinds!: string[]
  private kind = {
    value: '',
    error: false,
    errorMessage: ''
  }

  private file: File = new File([''], '')

  public async importFile (): Promise<void> {
    if (this.importIsLoading || this.kind.value === '' || this.file.name === '') {
      await this.$store?.dispatch('TransactionsService/setErrorMessage', 'Proszę uzupełnić brakujące dane')
      return
    }
    await this.$store?.dispatch('TransactionsService/setErrorMessage', '')

    const params: ImportFileParams = {
      file: this.file,
      kind: this.kind.value
    }

    const { data } = await this.$store.dispatch('TransactionsService/import', params)

    setTimeout(() => {
      window.location.replace(data)
    }, 1000)

    if (this.errors.length === 0) {
      window.location.replace('/admin/transactions?kind=' + this.kind.value)
      this.$emit('close')
    }
  }

  public async previewFiles (event: Event): Promise<void> {
    const target = event.target as HTMLInputElement
    this.file = (target.files as FileList)[0]
  }

  public get importIsLoading (): string {
    return this.$store?.getters['TransactionsService/importIsLoading']
  }

  public get errors (): string {
    return this.$store?.getters['TransactionsService/error']
  }
}
</script>

<style scoped lang="scss">
  @import "@/assets/styles/main";

  .c-fileUploader {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 30px;
    left: 0;
    background: rgba(255, 255, 255, 0.7);
    z-index: $index-modal;
  }

  .close {
    position: absolute;
    right: 10px;
    top: 20px;
  }

  .modal-title {
    font-size: 18px;
    line-height: 30px;
    color: $cl-grey5;
    font-weight: 700;
    margin: 5px 0;
  }

  .modal-body {
    margin-left: 30px;
    margin-right: 30px;
  }

  .modal {
    display: block !important;
  }

  .file-group {
    label {
      padding: 1rem;
    }
  }

  .loader,
  .loader::before,
  .loader::after {
    border-radius: 50%;
  }

  .loader {
    color: $cl-whiteff;
    font-size: 11px;
    text-indent: -99999em;
    margin: 55px auto;
    position: relative;
    width: 10em;
    height: 10em;
    box-shadow: inset 0 0 0 1em;
    -webkit-transform: translateZ(0);
    -ms-transform: translateZ(0);
    transform: translateZ(0);
  }

  .loader::before,
  .loader::after {
    position: absolute;
    content: '';
  }

  .loader::before {
    width: 5.2em;
    height: 10.2em;
    background: $cl-blue2c;
    border-radius: 10.2em 0 0 10.2em;
    top: -0.1em;
    left: -0.1em;
    -webkit-transform-origin: 5.1em 5.1em;
    transform-origin: 5.1em 5.1em;
    -webkit-animation: load2 2s infinite ease 1.5s;
    animation: load2 2s infinite ease 1.5s;
  }

  .loader::after {
    width: 5.2em;
    height: 10.2em;
    background: $cl-blue2c;
    border-radius: 0 10.2em 10.2em 0;
    top: -0.1em;
    left: 4.9em;
    -webkit-transform-origin: 0.1em 5.1em;
    transform-origin: 0.1em 5.1em;
    -webkit-animation: load2 2s infinite ease;
    animation: load2 2s infinite ease;
  }

  @-webkit-keyframes load2 {
    0% {
      -webkit-transform: rotate(0deg);
      transform: rotate(0deg);
    }

    100% {
      -webkit-transform: rotate(360deg);
      transform: rotate(360deg);
    }
  }

  @keyframes load2 {
    0% {
      -webkit-transform: rotate(0deg);
      transform: rotate(0deg);
    }

    100% {
      -webkit-transform: rotate(360deg);
      transform: rotate(360deg);
    }
  }

  .file-label {
    padding: 0.5rem;
  }

</style>
