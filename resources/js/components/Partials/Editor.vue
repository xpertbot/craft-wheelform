<template>
    <div class="wheelform-editor">
        <textarea ref="textarea" :name="name" :placeholder="placeholder"></textarea>
    </div>
</template>

<script>
import _CodeMirror from 'codemirror';
const CodeMirror = window.CodeMirror || _CodeMirror;

export default {
    data() {
        return {
            content: '',
            codemirror: null,
            cminstance: null,
        }
    },
    props: {
      value: String,
      marker: Function,
      unseenLines: Array,
      name: {
        type: String,
        default: 'codemirror'
      },
      placeholder: {
        type: String,
        default: ''
      },
      options: {
        type: Object,
        default: () => ({})
      },
      events: {
        type: Array,
        default: () => ([])
      },
      globalEvents: {
        type: Array,
        default: () => ([])
      }
    },
    watch: {
      options: {
        deep: true,
        handler(options) {
          for (const key in options) {
            this.cminstance.setOption(key, options[key])
          }
        }
      },
      value(newVal) {
        this.handerCodeChange(newVal)
      },
    },
    methods: {
      initialize() {
        const cmOptions = this.options;
        this.codemirror = CodeMirror.fromTextArea(this.$refs.textarea, cmOptions)
        this.cminstance = this.codemirror
        this.cminstance.setValue(this.value || this.content)

        this.cminstance.on('change', cm => {
          this.content = cm.getValue()
          if (this.$emit) {
            this.$emit('input', this.content)
          }
        })

        const tmpEvents = {}
        const allEvents = [
          'scroll',
          'changes',
          'beforeChange',
          'cursorActivity',
          'keyHandled',
          'inputRead',
          'electricInput',
          'beforeSelectionChange',
          'viewportChange',
          'swapDoc',
          'gutterClick',
          'gutterContextMenu',
          'focus',
          'blur',
          'refresh',
          'optionChange',
          'scrollCursorIntoView',
          'update'
        ]
        .concat(this.events)
        .concat(this.globalEvents)
        .filter(e => (!tmpEvents[e] && (tmpEvents[e] = true)))
        .forEach(event => {
          this.cminstance.on(event, (...args) => {
            this.$emit(event, ...args)
            const lowerCaseEvent = event.replace(/([A-Z])/g, '-$1').toLowerCase()
            if (lowerCaseEvent !== event) {
              this.$emit(lowerCaseEvent, ...args)
            }
          })
        })
        this.$emit('ready', this.codemirror)
        this.unseenLineMarkers()
        // prevents funky dynamic rendering
        this.refresh()
      },
      refresh() {
        this.$nextTick(() => {
          this.cminstance.refresh()
        })
      },
      destroy() {
        // garbage cleanup
        const element = this.cminstance.doc.cm.getWrapperElement()
        element && element.remove && element.remove()
      },
      handerCodeChange(newVal) {
        const cm_value = this.cminstance.getValue()
        if (newVal !== cm_value) {
          const scrollInfo = this.cminstance.getScrollInfo()
          this.cminstance.setValue(newVal)
          this.content = newVal
          this.cminstance.scrollTo(scrollInfo.left, scrollInfo.top)
        }
        this.unseenLineMarkers()
      },
      unseenLineMarkers() {
        if (this.unseenLines !== undefined && this.marker !== undefined) {
          this.unseenLines.forEach(line => {
            const info = this.cminstance.lineInfo(line)
            this.cminstance.setGutterMarker(line, 'breakpoints', info.gutterMarkers ? null : this.marker())
          })
        }
      },
    },
    mounted() {
      this.initialize()
    },
    beforeDestroy() {
      this.destroy()
    }
}
</script>

