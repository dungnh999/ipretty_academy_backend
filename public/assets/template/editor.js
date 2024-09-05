function editorTemplate(id, toolbar){
  return new Quill(id , {
    theme: 'snow',
    modules: {
      toolbar: {
        container: toolbar,  // Selector for toolbar container
      }
    }
  });
}
