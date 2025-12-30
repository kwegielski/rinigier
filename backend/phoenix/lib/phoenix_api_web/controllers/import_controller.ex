defmodule PhoenixApiWeb.ImportController do
  use PhoenixApiWeb, :controller

  alias PhoenixApi.Import.UserImporter

  @api_token "supersecret123"

  def import(conn, %{"token" => token_param}) do
    if token_param == @api_token do
      users = UserImporter.import()
      json(conn, %{status: "ok", imported: length(users)})
    else
      conn
      |> put_status(:unauthorized)
      |> json(%{error: "Invalid token"})
    end
  end
end