const { createCanvas } = require('canvas')
const tinycolor = require('tinycolor2')

function luminance(hex){
  return tinycolor(hex).getLuminance()
}

function contrast(a,b){
  const la = luminance(a)
  const lb = luminance(b)
  const L1 = Math.max(la,lb)
  const L2 = Math.min(la,lb)
  return (L1+0.05)/(L2+0.05)
}

const pairs = [
  {fg:'#ffffff', bg:'#0F9B79', name:'white on semhys-500'},
  {fg:'#ffffff', bg:'#0b654e', name:'white on semhys-700'},
  {fg:'#0f172a', bg:'#ffffff', name:'brand-dark on white'},
  {fg:'#ffffff', bg:'#F36C13', name:'white on accent'},
  {fg:'#ffffff', bg:'#d85b10', name:'white on accent-600 (darker)'},
  {fg:'#0f172a', bg:'#F36C13', name:'brand-dark on accent (dark text on orange)'}
]

pairs.forEach(p=>{
  console.log(p.name, 'contrast:', contrast(p.fg,p.bg).toFixed(2))
})
