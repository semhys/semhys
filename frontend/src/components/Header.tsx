import React, { useState, useRef, useEffect } from 'react'
import { Link } from 'react-router-dom'

export default function Header() {
  const [open, setOpen] = useState(false)
  const firstLinkRef = useRef<HTMLAnchorElement | null>(null)
  useMobileMenuEffects(open, firstLinkRef, setOpen)
  return (
    <header className="bg-white border-b shadow-sm relative z-30">
      <div className="container mx-auto px-4 py-4 flex items-center justify-between">
        <Link to="/" className="flex items-center gap-3">
          <img src="/assets/img/logo.svg" alt="SEMHYS" className="w-10 h-10 rounded-full bg-white shadow-sm"/>
          <span className="font-extrabold text-xl bg-gradient-to-r from-accent-500 to-semhys-500 bg-clip-text text-transparent">SEMHYS</span>
        </Link>

        <nav className="hidden md:flex space-x-6 text-sm text-brand-muted items-center">
          <Link to="/about">Nosotros</Link>
          <Link to="/services">Servicios</Link>
          <Link to="/blog">Blog</Link>
          <Link to="/contact" className="text-brand-dark font-medium"> <button className="btn-accent">Contacto</button> </Link>
        </nav>

        <div className="md:hidden">
          <button aria-label={open? 'Cerrar menú' : 'Abrir menú'} aria-expanded={open} className="p-2 rounded-md" onClick={()=>setOpen(v=>!v)}>
            {open ? (
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden>
                <path d="M6 6L18 18" stroke="#0f172a" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                <path d="M6 18L18 6" stroke="#0f172a" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
              </svg>
            ) : (
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden>
                <path d="M4 6H20" stroke="#0f172a" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                <path d="M4 12H20" stroke="#0f172a" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                <path d="M4 18H20" stroke="#0f172a" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
              </svg>
            )}
          </button>
        </div>
      </div>

      {/* Mobile panel */}
      {open && (
        <div className="md:hidden">
          <div className="mobile-nav-backdrop" onClick={()=>setOpen(false)}></div>
          <div className="absolute right-4 left-4 top-20 bg-white rounded-lg shadow-lg p-4 z-40 animate-mobile-open">
            <nav className="flex flex-col gap-3 text-brand-dark">
              <Link ref={firstLinkRef} to="/about" onClick={()=>setOpen(false)}>Nosotros</Link>
              <Link to="/services" onClick={()=>setOpen(false)}>Servicios</Link>
              <Link to="/blog" onClick={()=>setOpen(false)}>Blog</Link>
              <Link to="/contact" onClick={()=>setOpen(false)} className="font-medium">Contacto</Link>
            </nav>
          </div>
        </div>
      )}
    </header>
  )
}

// Side effects: lock scroll, focus management and Escape to close
function useMobileMenuEffects(open:boolean, firstLinkRef:React.RefObject<HTMLAnchorElement | null>, setOpen:(v:boolean)=>void){
  useEffect(()=>{
    // lock scroll when open
    if(open){
      const prev = document.body.style.overflow
      document.body.style.overflow = 'hidden'
      return ()=>{ document.body.style.overflow = prev }
    }
  },[open])

  useEffect(()=>{
    function onKey(e:KeyboardEvent){
      if(e.key === 'Escape' && open) setOpen(false)
    }
    document.addEventListener('keydown', onKey)
    return ()=> document.removeEventListener('keydown', onKey)
  },[open,setOpen])

  useEffect(()=>{
    if(open && firstLinkRef.current){
      firstLinkRef.current.focus()
    }
  },[open, firstLinkRef])
}
