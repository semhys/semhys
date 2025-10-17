import React from 'react'

export default function Contact(){
  return (
    <div>
      <h2 className="text-2xl font-bold">Contact</h2>
      <p className="mt-4 text-gray-600">Use this form to reach our team.</p>
      <form className="mt-4 max-w-md">
        <label className="block mb-2">Name<input className="w-full border p-2 rounded"/></label>
        <label className="block mb-2">Email<input className="w-full border p-2 rounded"/></label>
        <label className="block mb-2">Message<textarea className="w-full border p-2 rounded"/></label>
        <button className="mt-2 px-4 py-2 bg-blue-600 text-white rounded">Send</button>
      </form>
    </div>
  )
}
