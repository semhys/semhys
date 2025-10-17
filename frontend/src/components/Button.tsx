import React from 'react'

export default function Button({ children, onClick, variant = 'primary', disabled = false }:{children:React.ReactNode; onClick?: () => void; variant?: 'primary'|'ghost'|'accent'; disabled?: boolean}){
  const cls = ['btn']
  if(variant === 'primary') cls.push('btn-primary')
  else if(variant === 'accent') cls.push('btn-accent')
  else cls.push('btn-ghost')
  if(disabled) cls.push('opacity-60 pointer-events-none')
  return <button className={cls.join(' ')} onClick={onClick} aria-disabled={disabled}>{children}</button>
}
