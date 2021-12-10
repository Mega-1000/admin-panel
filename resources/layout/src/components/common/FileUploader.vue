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
        <div class="modal-body">
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
                <div class="col-md-5">
                  <div class="input-group-file">
                    <button class="btn btn-file">Dodaj plik<input id="file" ref="uploadFiles" @change="previewFiles"
                                                                  type="file" aria-describedby="file">
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" @click="importFile">Importuj</button>
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
    if (this.kind.value === '') {
      return
    }

    const params: ImportFileParams = {
      file: this.file,
      kind: this.kind.value
    }

    await this.$store.dispatch('TransactionsService/import', params)
    // this.$emit('close')
  }

  public async previewFiles (event: any) {
    this.file = event.target.files[0]
    console.log(this.file)
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
</style>
