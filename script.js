function likeRecipe(recipeId) {
  fetch(`like_recipe.php?id=${recipeId}`)
    .then((response) => response.text())
    .then((data) => {
      document.getElementById(`likes-${recipeId}`).innerText = data;
    });
}

function saveRecipe(recipeId) {
  fetch(`save_recipe.php?id=${recipeId}`).then((response) =>
    alert("Recipe saved successfully!")
  );
}
